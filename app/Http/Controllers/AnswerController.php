<?php
    declare( strict_types = 1 );

    namespace App\Http\Controllers;

    use App\Http\Requests\AcceptAnswerRequest;
    use App\Http\Requests\StoreAnswerRequest;
    use App\Http\Requests\UpdateAnswerRequest;
    use App\Models\Answer;
    use App\Models\Ticket;
    use App\Services\AnswerService;
    use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\View\View;

    class AnswerController extends Controller
    {
        use AuthorizesRequests;

        private AnswerService $service;

        public function __construct( AnswerService $service )
        {
            $this->service = $service;
        }

        public function create( Ticket $ticket )
        {
            $this->authorize( 'answer', $ticket );
            return view( 'tickets.answers.create', compact( 'ticket' ) );
        }

        public function store( StoreAnswerRequest $request, Ticket $ticket ) : RedirectResponse
        {
            $this->authorize( 'answer', $ticket );
            $answer = $this->service->create( array_merge( $request->validated(), [ 'ticket_id' => $ticket->id ] ) );
            return redirect()->route( 'tickets.show', $ticket )
                ->with( 'success', __( 'answers.submitted_successfully' ) );
        }

        public function edit( Answer $answer )
        {
            $this->authorize( 'update', $answer );
            return view( 'tickets.answers.edit', compact( 'answer'  ) );
        }

        public function update( UpdateAnswerRequest $request, Answer $answer ) : RedirectResponse
        {
            $this->authorize( 'update', $answer );
            $updatedAnswer = $this->service->update( $answer->id, $request->validated() );
            return redirect()->route( 'tickets.show', $answer->ticket )
                ->with( 'success', __( 'answers.updated_successfully' ) );
        }

        public function destroy( Answer $answer ) : RedirectResponse
        {
            $this->authorize( 'delete', $answer );
            $ticket = $answer->ticket;
            $this->service->delete( $answer->id );
            return redirect()->route( 'tickets.show', $ticket )
                ->with( 'success', __( 'answers.deleted_successfully' ) );
        }

        public function accept( Answer $answer ) : RedirectResponse
        {
            $this->authorize( 'accept', $answer );
            $this->service->acceptAnswer( $answer );
            return redirect()->route( 'tickets.show', $answer->ticket )
                ->with( 'success', __( 'answers.accepted_successfully' ) );
        }
    }