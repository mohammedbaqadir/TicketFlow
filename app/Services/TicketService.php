<?php

    namespace App\Services;

    use App\Models\Solution;
    use App\Models\Ticket;
    use App\Models\User;
    use App\Helpers\AuthHelper;
    use Exception;
    use GeminiAPI\Laravel\Facades\Gemini;
    use Illuminate\Support\Facades\Log;
    use Spatie\Activitylog\Models\Activity;

    class TicketService
    {
        /**
         * Assign a ticket to a user based on the given action and user role.
         *
         * @param  Ticket  $ticket
         * @param  array  $data
         * @return Ticket
         */
        public function assignTicket( Ticket $ticket, array $data ) : void
        {
            $user = auth()->user();

            // Check if the user is an agent and assign the ticket to themselves
            if ( AuthHelper::userHasRole( 'agent' ) ) {
                $this->assignToUser( $ticket, $user );
            } elseif ( AuthHelper::userHasRole( 'admin' ) ) {
                // If the user is an admin, assign the ticket based on the action
                if ( $data['action'] === 'assign_to_self' ) {
                    $this->assignToUser( $ticket, $user );
                } elseif ( $data['action'] === 'assign_to_agent' ) {
                    $agent = User::findOrFail( $data['agent_id'] );
                    $this->assignToUser( $ticket, $agent, 'Admin' );
                }
            }
        }

        /**
         * Assign a ticket to a specific user and create appropriate events.
         *
         * @param  Ticket  $ticket
         * @param  User  $user
         * @param  string|null  $assignedBy
         * @return void
         */
        private function assignToUser( Ticket $ticket, User $user, ?string $assignedBy = null ) : void
        {
            $ticket->update( ['assigned_to' => $user->id] );
            $assignedByText = $assignedBy ? " by $assignedBy" : '';

            activity()
                ->on( $ticket )
                ->by( $user)
                ->log( "Ticket assigned to {$user->name}.{$assignedByText}");

            $ticket->update([ 'status' => 'in-progress']);
        }

        /**
         * Unassign a ticket and create appropriate events.
         *
         * @param  Ticket  $ticket
         * @return void
         */
        public function unassignTicket( Ticket $ticket, User $user ) : void
        {
            $ticket->update( ['assigned_to' => null] );
            activity()
                ->on( $ticket )
                ->by( $user )
                ->log( "{$user->name} un-assigned from ticket" );

            $ticket->update( [ 'status' => 'open' ] );
        }

        /**
         * Submit a solution for a ticket.
         *
         * @param  Ticket  $ticket
         * @param  array  $data
         * @return void
         */
        public function submitSolution( Ticket $ticket, array $data )
        {
            $assignee = $ticket->assignee;
            $solution = Solution::create( [
                'ticket_id' => $ticket->id,
                'user_id' => $assignee->id,
                'content' => $data['content'],
            ] );

            if ( isset( $data['solution_attachments'] ) ) {
                foreach ( $data['solution_attachments'] as $file ) {
                    $solution->addMedia( $file )->toMediaCollection( 'solution_attachments' );
                }
            }
            activity()
                ->on( $ticket )
                ->by( $assignee )
                ->log( 'Solution submitted by ' . $assignee );

            $ticket->update( [ 'status' => 'awaiting-acceptance' ] );
        }

        /**
         * Determine the timeout duration based on the ticket's priority.
         *
         * @param  string  $priority
         * @return int
         */
        public static function determineTimeout( string $priority ) : int
        {
            return match ( $priority ) {
                'high' => 2,
                'medium' => 4,
                'low' => 8,
                default => 8,
            };
        }

        /**
         * Determine the priority of a ticket based on its title and description.
         *
         * @param  string  $title
         * @param  string  $description
         * @return string
         */
        public static function determinePriority( string $title, string $description ) : string
        {
            $prompt = "
    **Input:**
        * Title: $title 
        * Description: $description  
    **Context:**
        This prompt simulates an **IT ticketing system** where an employee has submitted a ticket requesting assistance.     
    **Instructions:**
        1. **Analyze Impact:**
            - Identify keywords in the title and description that indicate the severity of the issue's impact on the employee's ability to perform their job functions.
        
            **High Impact Keywords:**
                * System outage (server crash, network failure, application down)
                * Data loss or corruption
                * Financial impact (payment processing errors, billing issues)
                * Complete halt of critical business processes
                * Security breaches or potential security vulnerabilities
            
            **Medium Impact Keywords:**
                * Degraded performance (slow loading times, lag, intermittent functionality)
                * Limited access to critical data or systems
                * Inability to complete specific tasks or workflows
                * Disruptions to communication or collaboration
                * Hardware or software malfunctions impacting productivity
            
            **Low Impact Keywords:**
                * Minor formatting or display issues
                * Password reset requests
                * User interface glitches or inconveniences
                * Non-critical permission or access requests
                * Information requests or clarifications
        
        2. **Apply Priority Rules:**
            - If the title or description mentions high impact keywords, assign \"High\" priority.
            - If the title or description mentions medium impact keywords, assign \"Medium\" priority.
            - If no high or medium impact keywords are found, assign \"Low\" priority.
        
        3. **Consider Context:**
            - Use common sense to interpret the overall impact of the issue, even if specific keywords aren't present.
            - For example, a seemingly minor formatting issue might be critical for a presentation.
        
        **Output:**
        
        **As the IT ticketing system,** assign a one-word priority label (High, Medium, or Low) based on the analyzed impact on the employee's ability to work.";

            try {
                $response = Gemini::generateText( $prompt );
                return strtolower( preg_replace( '/[^a-zA-Z]/', '', $response ) );
            } catch (Exception $e) {
                Log::error( 'API error while determining priority: ' . $e->getMessage() );
                return 'low';
            }
        }
    }