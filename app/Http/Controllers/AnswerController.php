<?php
    declare( strict_types = 1 );

    namespace App\Http\Controllers;

    use App\Actions\Answer\AcceptAnswerAction;
    use App\Actions\Answer\CreateAnswerAction;
    use App\Actions\Answer\DeleteAnswerAction;
    use App\Actions\Answer\UpdateAnswerAction;
    use App\Http\Requests\StoreAnswerRequest;
    use App\Http\Requests\UpdateAnswerRequest;
    use App\Models\Answer;
    use App\Models\Ticket;
    use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\View\View;

    class AnswerController extends Controller
    {

        use AuthorizesRequests;

        public function create( Ticket $ticket ) : View
        {
            $this->authorize( 'answer', $ticket );
            return view( 'tickets.answers.create', compact( 'ticket' ) );
        }

        public function store(
            StoreAnswerRequest $request,
            Ticket $ticket,
            CreateAnswerAction $createAnswerAction
        ) : RedirectResponse {
            $this->authorize( 'answer', $ticket );
            $validatedData = $request->validated();

            /** @var array{content: string} $validatedData */
            $updatedTicket = $createAnswerAction->execute( [
                'content' => (string) $validatedData['content'],
                'ticket_id' => (int) $ticket->id,
            ] );

            return redirect()->route( 'tickets.show', $updatedTicket )
                ->withToast( 'Submitted!', 'Your answer was submitted successfully', 'success' );
        }

        public function edit( Answer $answer ) : View
        {
            $this->authorize( 'update', $answer );
            return view( 'tickets.answers.edit', compact( 'answer' ) );
        }

        public function update(
            UpdateAnswerRequest $request,
            Answer $answer,
            UpdateAnswerAction $updateAnswerAction
        ) : RedirectResponse {
            $this->authorize( 'update', $answer );
            $validatedData = $request->validated();

            /** @var array{content: string} $validatedData */
            $updatedTicket = $updateAnswerAction->execute( $answer, [
                'content' => (string) $validatedData['content'],
            ] );


            return redirect()->route( 'tickets.show', $updatedTicket )
                ->withToast( 'updated!', 'Your answer was updated successfully', 'success' );
        }

        public function destroy( Answer $answer, DeleteAnswerAction $deleteAnswerAction ) : RedirectResponse
        {
            $this->authorize( 'delete', $answer );
            $updatedTicket = $deleteAnswerAction->execute( $answer );

            return redirect()->route( 'tickets.show', $updatedTicket )
                ->withToast( 'Deleted!', 'Your answer was deleted successfully.', 'danger' );
        }

        public function accept( Answer $answer, AcceptAnswerAction $acceptAnswerAction ) : RedirectResponse
        {
            $this->authorize( 'accept', $answer );
            $updatedTicket = $acceptAnswerAction->execute( $answer );

            return redirect()->route( 'tickets.show', $updatedTicket )
                ->withToast( 'Accepted!', 'Your ticket is resolved.', 'success' );
        }

    }