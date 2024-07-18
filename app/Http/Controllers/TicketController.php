<?php
    declare( strict_types = 1 );

    namespace App\Http\Controllers;

    use App\Http\Requests\StoreTicketRequest;
    use App\Http\Requests\UpdateTicketRequest;
    use App\Models\Answer;
    use App\Models\Ticket;
    use App\Services\TicketService;
    use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Str;
    use Illuminate\View\View;

    class TicketController extends Controller
    {
        use AuthorizesRequests;

        private TicketService $service;

        public function __construct( TicketService $service )
        {
            $this->service = $service;
        }

        public function index( Request $request ) : View
        {
            $this->authorize( 'viewAny', Ticket::class );
            $tickets = $this->service->getAll( [ 'relations' => [ 'requestor', 'assignee' ] ] );
            return view( 'tickets.index', compact( 'tickets' ) );
        }

        public function create() : View
        {
            $this->authorize( 'create', Ticket::class );
            return view( 'tickets.create' );
        }

        public function store( StoreTicketRequest $request ) : RedirectResponse
        {
            $this->authorize( 'create', Ticket::class );
            $ticket = $this->service->create( $request->validated() );

            return redirect()->route( 'tickets.show', $ticket )
                ->with( 'success', __( 'tickets.created_successfully' ) );
        }

        public function show( Ticket $ticket ) : View
        {
            $this->authorize( 'view', $ticket );

            $ticket->load( [ 'requestor', 'assignee', 'answers', 'comments' ] );
            return view( 'tickets.show', compact( 'ticket' ) );
        }

        public function meeting( Ticket $ticket ) : ?JsonResponse
        {
            $this->authorize( 'view', $ticket );

            $magicCookie = config( 'services.jitsi.vpaas_magic_cookie' );

            if ( $ticket->meeting_room ) {
                $roomName = $ticket->meeting_room;
            } else {
                $roomName = $magicCookie . '/ticket-' . $ticket->id . '-' . Str::random( 10 );

                $ticket->update( [ 'meeting_room' => $roomName ] );
            }

            return response()->json( [
                'roomName' => $roomName,
                'ticketId' => $ticket->id,
                'assigneeName' => $ticket->assignee->name ?? null,
                'requestorName' => $ticket->requestor->name
            ] );
        }

        public function edit( Ticket $ticket ) : View
        {
            $this->authorize( 'update', $ticket );
            return view( 'tickets.edit', compact( 'ticket' ) );
        }

        public function update( UpdateTicketRequest $request, Ticket $ticket ) : RedirectResponse
        {
            $this->authorize( 'update', $ticket );
            $updatedTicket = $this->service->update( $ticket->id, $request->validated() );
            return redirect()->route( 'tickets.show', $updatedTicket )
                ->with( 'success', __( 'tickets.updated_successfully' ) );
        }

        public function destroy( Ticket $ticket ) : RedirectResponse
        {
            $this->authorize( 'delete', $ticket );
            $this->service->delete( $ticket->id );
            return redirect()->route( 'home' )
                ->with( 'success', __( 'tickets.deleted_successfully' ) );
        }

        public function myTickets( Request $request ) : View
        {
            $tickets = $this->service->getTicketsByUser(
                $request->user(),
                $request->only( [ 'filters', 'sort', 'per_page' ] )
            );
            return view( 'tickets.my-tickets', compact( 'tickets' ) );
        }

        public function assign( Ticket $ticket ) : RedirectResponse
        {
            $this->authorize( 'assign', $ticket );
            $this->service->assignTicket( $ticket, auth()->user() );
            return redirect()->route( 'tickets.show', $ticket )
                ->with( 'success', __( 'tickets.assigned_successfully' ) );
        }

        public function unassign( Ticket $ticket ) : RedirectResponse
        {
            $this->authorize( 'unassign', $ticket );
            $this->service->unassignTicket( $ticket );
            return redirect()->route( 'tickets.index')
                ->with( 'success', __( 'tickets.unassigned_successfully' ) );
        }
    }