<?php
    declare( strict_types = 1 );

    namespace App\Services;

    use App\Models\Ticket;
    use App\Models\User;
    use App\Repositories\TicketRepository;
    use Exception;
    use GeminiAPI\Laravel\Facades\Gemini;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;

    class TicketService
    {
        protected TicketRepository $repository;

        public function __construct( TicketRepository $repository ,  )
        {
            $this->repository = $repository;
        }

        public function getAll( array $options = [] ) : LengthAwarePaginator
        {
            return $this->repository->getAll(
                $options['filters'] ?? [],
                $options['sort'] ?? [ 'created_at' => 'desc' ],
                $options['per_page'] ?? 15,
                $options['relations'] ?? []
            );
        }

        public function getById( int $id, array $relations = [] ) : ?Ticket
        {
            return $this->repository->getById( $id, $relations );
        }

        public function create( array $data ) : Ticket
        {
            return DB::transaction( function () use ( $data ) {
                $ticketData = $this->prepareTicketData( $data );
                $ticket = $this->repository->create( $ticketData );
                return $ticket->fresh( [ 'requestor', 'assignee', 'answers', 'comments' ] );
            } );
        }

        public function update( int $id, array $data ) : Ticket
        {
            return DB::transaction( function () use ( $id, $data ) {
                $ticket = $this->repository->getById( $id );
                if ( !$ticket ) {
                    throw new \Exception( "Ticket with ID {$id} not found" );
                }

                $ticketData = $this->prepareTicketData( $data, false );
                $this->repository->update( $ticket, $ticketData );


                return $ticket->fresh( [ 'requestor', 'assignee', 'answers', 'comments' ] );
            } );
        }

        public function delete( int $id ) : bool
        {
            return DB::transaction( function () use ( $id ) {
                $ticket = $this->repository->getById( $id );
                if ( !$ticket ) {
                    throw new \Exception( "Ticket with ID {$id} not found" );
                }
                return $this->repository->delete( $ticket );
            } );
        }

        public function assignTicket( Ticket $ticket, User $user ) : Ticket
        {
            return DB::transaction( function () use ( $ticket, $user ) {
                $this->repository->update( $ticket, [
                    'assignee_id' => $user->id,
                    'status' => 'in-progress'
                ] );
                return $ticket->fresh( [ 'requestor', 'assignee', 'answers', 'comments' ] );
            } );
        }

        public function unassignTicket( Ticket $ticket ) : Ticket
        {
            return DB::transaction( function () use ( $ticket ) {
                $this->repository->update( $ticket, [
                    'assignee_id' => null,
                    'status' => 'open'
                ] );
                return $ticket->fresh( [ 'requestor', 'assignee', 'answers', 'comments' ] );
            } );
        }

        public function getTicketsByUser( User $user, array $options = [] ) : LengthAwarePaginator
        {
            return $this->repository->getTicketsByUser(
                $user,
                $options['filters'] ?? [],
                $options['sort'] ?? [ 'created_at' => 'desc' ],
                $options['per_page'] ?? 15,
                $options['relations'] ?? []
            );
        }

        public function getAssignedTicketsByUser( User $user, array $options = [] ) : LengthAwarePaginator
        {
            return $this->repository->getAssignedTicketsByUser(
                $user,
                $options['filters'] ?? [],
                $options['sort'] ?? [ 'created_at' => 'desc' ],
                $options['per_page'] ?? 15,
                $options['relations'] ?? []
            );
        }

        private function prepareTicketData( array $data, bool $isNewTicket = true ) : array
        {
            $ticketData = [
                'title' => $data['title'],
                'description' => $data['description'],
                'priority' => $data['priority'] ?? $this->determinePriority( $data['title'], $data['description'] ),
                'timeout_at' => now()->addHours( $this->determineTimeout( $data['priority'] ?? 'low' ) ),
            ];

            if ( $isNewTicket ) {
                $ticketData['requestor_id'] = auth()->id();
                $ticketData['status'] = 'open';
            }

            return array_filter( $ticketData, static function ( $value ) {
                return $value !== null;
            } );
        }

        public function determineTimeout( string $priority ) : int
        {
            return match ( $priority ) {
                'high' => 2,
                'medium' => 4,
                default => 8,
            };
        }

        public function determinePriority( string $title, string $description ) : string
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