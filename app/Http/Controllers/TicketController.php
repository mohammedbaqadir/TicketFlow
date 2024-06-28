<?php
    declare( strict_types = 1 );

    namespace App\Http\Controllers;

    use App\Http\Requests\StoreTicketRequest;
    use App\Http\Requests\UpdateTicketRequest;
    use App\Models\Solution;
    use App\Models\Ticket;
    use App\Services\SolutionService;
    use App\Services\TicketService;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Log;

    class TicketController extends Controller
    {
        protected TicketService $ticketService;

        public function __construct( TicketService $ticketService )
        {
            $this->ticketService = $ticketService;
        }

        public function create()
        {
            return view( 'tickets.create' );
        }

        public function store( StoreTicketRequest $request )
        {
            $ticket = $this->ticketService->createTicket( $request->validated(), Auth::user() );
            return redirect()->route( 'tickets.show', $ticket )->with( 'success', 'Ticket created successfully.' );
        }

        public function show( Ticket $ticket )
        {
            return view( 'tickets.show', compact( 'ticket' ) );
        }

        public function edit( Ticket $ticket )
        {
            return view( 'tickets.edit', compact( 'ticket' ) );
        }

        public function update( UpdateTicketRequest $request, Ticket $ticket )
        {
            $result = $this->ticketService->updateTicket( $ticket, $request->validated() );
            if ( $result['success'] ) {
                return redirect()->route( 'tickets.show', $ticket )->with( 'success', 'Ticket updated successfully.' );
            }
            return back()->withErrors( $result['message'] );
        }

        public function destroy( Ticket $ticket )
        {
            $result = $this->ticketService->deleteTicket( $ticket );
            if ( $result ) {
                return redirect()->route( 'my-tickets' )->with( 'success', 'Ticket deleted successfully.' );
            }
            return back()->withErrors( 'Failed to delete the ticket.' );
        }


        public function myTickets()
        {
            $response = null;

            $user = Auth::user();
            if ( $user ) {
                try {
                    // Fetch the user's created tickets with eager loading
                    $tickets = $user->createdTickets()->with( [
                        'solutions', 'media', 'requestor', 'assignee'
                    ] )->get();

                    // Prepare data for view
                    $ticketGroups = $this->prepareTicketGroups( $tickets );
                    $response = view( 'tickets.my-tickets', [ 'ticketGroups' => $ticketGroups ] );
                } catch (\Exception $e) {
                    Log::error( 'Error fetching tickets: ' . $e->getMessage() );
                    $response = redirect()->route( 'error' )->withErrors( [ 'msg' => 'There was an issue fetching your tickets. Please try again later.' ] );
                }
            } else {
                $response = redirect()->route( 'login' )->withErrors( [ 'msg' => 'You must be logged in to view your tickets.' ] );
            }

            return $response;
        }

        private function prepareTicketGroups( $tickets ) : array
        {
            return [
                [
                    'title' => 'Pending Action',
                    'tickets' => $tickets->filter( fn( $ticket ) => $ticket->status === 'awaiting-acceptance' ),
                    'no_tickets_msg' => 'no tickets are pending action from you',
                ],
                [
                    'title' => 'On-Going',
                    'tickets' => $tickets->filter( fn( $ticket ) => \in_array( $ticket->status,
                        [ 'open', 'in-progress', 'elevated' ] ) ),
                    'no_tickets_msg' => "you don't have ongoing tickets",
                ],
                [
                    'title' => 'Resolved',
                    'tickets' => $tickets->filter( fn( $ticket ) => $ticket->status === 'closed' ),
                    'no_tickets_msg' => "you don't have any closed tickets yet",
                ]
            ];
        }
    }