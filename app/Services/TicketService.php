<?php
    declare( strict_types = 1 );

    namespace App\Services;

    use App\Models\Solution;
    use App\Models\Ticket;
    use App\Models\User;
    use App\Helpers\AuthHelper;
    use Exception;
    use GeminiAPI\Laravel\Facades\Gemini;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;
    use Spatie\MediaLibrary\MediaCollections\Models\Media;

    class TicketService
    {
        public function createTicket( array $data, User $user ) : Ticket
        {
            DB::beginTransaction();
            try {
                $ticket = Ticket::create( [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'status' => 'open',
                    'priority' => $this->determinePriority( $data['title'], $data['description'] ),
                    'created_by' => $user->id,
                    'timeout_at' => now()->addHours( $this->determineTimeout( $data['priority'] ) ),
                ] );

                if ( isset( $data['attachments'] ) ) {
                    foreach ( $data['attachments'] as $attachment ) {
                        $ticket->addMedia( $attachment )->toMediaCollection( 'ticket_attachments' );
                    }
                }

                DB::commit();
                return $ticket;
            } catch (Exception $e) {
                DB::rollBack();
                Log::error( 'Error creating ticket: ' . $e->getMessage() );
                throw $e;
            }
        }


        public function updateTicket( Ticket $ticket, array $data ) : array
        {
            DB::beginTransaction();
            try {
                $ticket->update( [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'priority' => $this->determinePriority( $data['title'], $data['description'] ),
                    'timeout_at' => now()->addHours( $this->determineTimeout( $ticket->priority ) ),
                ] );

                if ( isset( $data['delete_attachments'] ) ) {
                    $ticket->deleteMedia( $data['delete_attachments'] );
                }

                if ( isset( $data['attachments'] ) ) {
                    foreach ( $data['attachments'] as $attachment ) {
                        $ticket->addMedia( $attachment )->toMediaCollection( 'ticket_attachments' );
                    }
                }

                DB::commit();
                return [
                    'success' => true,
                    'message' => 'Ticket updated successfully',
                    'ticket' => $ticket->fresh()->load( 'media' ),
                ];
            } catch (Exception $e) {
                DB::rollBack();
                Log::error( 'Error updating ticket: ' . $e->getMessage() );
                return [
                    'success' => false,
                    'message' => 'An error occurred while updating the ticket',
                ];
            }
        }

        public function deleteTicket( Ticket $ticket ) : bool
        {
            try {
                return $ticket->delete();
            } catch (Exception $e) {
                Log::error( 'Error deleting ticket: ' . $e->getMessage() );
                return false;
            }
        }


        public function assignTicket( Ticket $ticket, User $user ) : void
        {
            $ticket->update( [ 'assigned_to' => $user->id, 'status' => 'in-progress' ] );
        }

        public function unassignTicket( Ticket $ticket ) : void
        {
            $ticket->update( [ 'assigned_to' => null, 'status' => 'open' ] );
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