<?php
    declare( strict_types = 1 );

    namespace App\Http\Controllers;

    use App\Http\Requests\StoreTicketRequest;
    use App\Http\Requests\UpdateTicketRequest;
    use App\Models\Ticket;
    use App\Services\TicketService;
    use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Log;
    use Illuminate\View\View;

    class TicketController extends Controller
    {
        use AuthorizesRequests;

        private TicketService $service;

        public function __construct( TicketService $service )
        {
            $this->service = $service;
            $this->authorizeResource( Ticket::class, 'ticket' );
        }

        public function index( Request $request ) : View
        {
            $tickets = $this->service->getAll( $request->only( [ 'filters', 'sort', 'per_page' ] ) );
            return view( 'tickets.index', compact( 'tickets' ) );
        }

        public function create() : View
        {
            return view( 'tickets.create' );
        }

        public function store( StoreTicketRequest $request ) : RedirectResponse
        {
            $ticket = $this->service->create( $request->validated() );
            return redirect()->route( 'tickets.show', $ticket )
                ->with( 'success', __( 'tickets.created_successfully' ) );
        }

        public function show( Ticket $ticket ) : View
        {
            $ticket->load( [ 'requestor', 'assignee', 'answers', 'comments' ] );
            return view( 'tickets.show', compact( 'ticket' ) );
        }

        public function edit( Ticket $ticket ) : View
        {
            return view( 'tickets.edit', compact( 'ticket' ) );
        }

        public function update( UpdateTicketRequest $request, Ticket $ticket ) : RedirectResponse
        {
            $updatedTicket = $this->service->update( $ticket->id, $request->validated() );
            return redirect()->route( 'tickets.show', $updatedTicket )
                ->with( 'success', __( 'tickets.updated_successfully' ) );
        }

        public function destroy( Ticket $ticket ) : RedirectResponse
        {
            $this->service->delete( $ticket->id );
            return redirect()->route( 'tickets.index' )
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
            return redirect()->route( 'tickets.show', $ticket )
                ->with( 'success', __( 'tickets.unassigned_successfully' ) );
        }
    }