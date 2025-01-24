<?php
    declare( strict_types = 1 );

    namespace App\Http\Controllers;

    use App\Actions\Ticket\AssignTicketAction;
    use App\Actions\Ticket\CreateTicketAction;
    use App\Actions\Ticket\DeleteTicketAction;
    use App\Actions\Ticket\GroupTicketsAction;
    use App\Actions\Ticket\UnassignTicketAction;
    use App\Actions\Ticket\UpdateTicketAction;
    use App\Config\TicketConfig;
    use App\Http\Requests\StoreTicketRequest;
    use App\Http\Requests\UpdateTicketRequest;
    use App\Models\Ticket;
    use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Str;
    use Illuminate\View\View;

    class TicketController extends Controller
    {
        use AuthorizesRequests;

        public function __construct(
            private readonly GroupTicketsAction $groupTicketsAction
        ) {
        }

        public function index() : View
        {
            $this->authorize( 'viewAny', Ticket::class );
            $tickets = Ticket::withRelations()
                ->paginate( 10 );
            $ticketGroups = $this->groupTicketsAction->execute(
                $tickets->getCollection(),
                TicketConfig::getIndexGroupings(),
                auth()->user()
            );
            return view( 'tickets.index', compact( 'ticketGroups', 'tickets' ) );
        }

        public function create() : View
        {
            $this->authorize( 'create', Ticket::class );
            return view( 'tickets.create' );
        }

        public function store( StoreTicketRequest $request, CreateTicketAction $createTicketAction ) : RedirectResponse
        {
            $this->authorize( 'create', Ticket::class );
            $validatedData = $request->validated();

            /** @var array{title: string, description: string} $validatedData */
            $ticket = $createTicketAction->execute( [
                'title' => (string) $validatedData['title'],
                'description' => (string) $validatedData['description'],
            ] );


            return redirect()->route( 'tickets.show', $ticket )
                ->withToast( 'Created!', 'Your ticket was Created successfully', 'success' );
        }

        public function show( Ticket $ticket ) : View
        {
            $this->authorize( 'view', $ticket );
            $cacheKey = "tickets_show_{$ticket->id}";
            if ( Cache::has( $cacheKey ) ) {
                $ticket = Cache::get( $cacheKey );
            } else {
                $ticket->withRelations()->with( [ 'answers' ] )->get();
            }
            return view( 'tickets.show', compact( 'ticket' ) );
        }

        public function meeting( Ticket $ticket ) : ?JsonResponse
        {
            try {
                $this->authorize( 'view', $ticket );
                $magicCookie = config( 'services.jitsi.vpaas_magic_cookie' );
                if ( !$magicCookie ) {
                    throw new \RuntimeException( 'Jitsi vpaas_magic_cookie is not configured.' );
                }

                $roomName = $ticket->meeting_room ?? $magicCookie . '/ticket-' . $ticket->id . '-' . Str::random( 10 );
                if ( !$ticket->meeting_room ) {
                    $ticket->update( [ 'meeting_room' => $roomName ] );
                }


                return response()->json( [
                    'roomName' => $roomName,
                    'ticketId' => $ticket->id,
                    'assigneeName' => $ticket->assignee->name ?? null,
                    'requestorName' => $ticket->requestor->name,
                ] );
            } catch (\Exception $e) {
                Log::error( 'Error initializing meeting: ' . $e->getMessage() );
                return response()->json( [ 'error' => 'Failed to initialize meeting' ], 500 );
            }
        }

        /**
         * Handle a user joining the meeting.
         *
         * @param  Request  $request
         * @return JsonResponse
         */
        public function meetingJoined( Request $request ) : JsonResponse
        {
            $validated = $request->validate( [
                'ticketId' => 'required|integer|exists:tickets,id',
                'meetingLink' => 'required|string',
                'username' => 'required|string',
            ] );

            // Retrieve the ticket by its ID
            $ticket = Ticket::findOrFail( $validated['ticketId'] );

            // Get the names of the requestor and assignee
            $requestorName = $ticket->requestor->name;
            $assigneeName = $ticket->assignee->name ?? null;

            // Determine who joined and who is still missing from the meeting
            $userWhoJoined = $validated['username'];
            $waitingForUser = null;

            // Check if the user who joined is the requestor, and if the assignee hasn't joined yet
            if ( $userWhoJoined === $requestorName && $assigneeName ) {
                $waitingForUser = $assigneeName; // The assignee is still waiting to join
            } elseif ( $userWhoJoined === $assigneeName ) {
                $waitingForUser = $requestorName; // The requestor is still waiting to join
            }
            Log::info( 'meeting joined triggered: ', [ $userWhoJoined, $waitingForUser ] );
            // If the other user hasn't joined, return a response indicating this
            if ( $waitingForUser ) {
                return response()->json( [
                    'waitingFor' => $waitingForUser, // Notify this user to join
                ] );
            }

            // Otherwise, just return a response that the user has joined
            return response()->json( [
                'message' => 'User joined the meeting',
            ] );
        }


        public function edit( Ticket $ticket ) : View
        {
            $this->authorize( 'update', $ticket );
            return view( 'tickets.edit', compact( 'ticket' ) );
        }

        public function update(
            UpdateTicketRequest $request,
            Ticket $ticket,
            UpdateTicketAction $updateTicketAction
        ) : RedirectResponse {
            $this->authorize( 'update', $ticket );
            $updatedTicket = $updateTicketAction->execute( $ticket, $request->validated() );

            return redirect()->route( 'tickets.show', $updatedTicket )
                ->withToast( 'Updated!', 'Your ticket was updated successfully.', 'success' );
        }

        public function destroy( Ticket $ticket, DeleteTicketAction $deleteTicketAction ) : RedirectResponse
        {
            $this->authorize( 'delete', $ticket );
            $deleteTicketAction->execute( $ticket );

            return redirect()->route( 'home' )
                ->withToast( 'Deleted!', 'Your ticket was deleted successfully.', 'danger' );
        }

        public function myTickets( Request $request ) : View
        {
            $tickets = Ticket::where( 'requestor_id', $request->user()->id )
                ->withRelations()
                ->paginate( 10 );

            $ticketGroups = $this->groupTicketsAction->execute(
                $tickets->getCollection(),
                TicketConfig::getMyTicketsGroupings()
            );

            return view( 'tickets.my-tickets', compact( 'ticketGroups', 'tickets' ) );
        }

        public function assign( Ticket $ticket, AssignTicketAction $assignTicketAction ) : RedirectResponse
        {
            $this->authorize( 'assign', $ticket );
            $assignTicketAction->execute( $ticket, auth()->user() );

            return redirect()->route( 'tickets.show', $ticket )
                ->withToast( 'Assigned!', 'You have been assigned to the ticket.', 'info' );
        }

        public function unassign( Ticket $ticket, UnassignTicketAction $unassignTicketAction ) : RedirectResponse
        {
            $this->authorize( 'unassign', $ticket );
            $unassignTicketAction->execute( $ticket );

            return redirect()->route( 'tickets.index' )
                ->withToast( 'Un-Assigned!', 'You have been un-assigned to the ticket.', 'info' );
        }
    }