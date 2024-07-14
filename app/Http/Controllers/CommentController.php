<?php
    declare( strict_types = 1 );

    namespace App\Http\Controllers;

    use App\Http\Requests\StoreCommentRequest;
    use App\Models\Answer;
    use App\Models\Comment;
    use App\Models\Ticket;
    use App\Services\CommentService;
    use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\View\View;

    class CommentController extends Controller
    {
        use AuthorizesRequests;

        private CommentService $service;

        public function __construct( CommentService $service )
        {
            $this->service = $service;
        }

        public function storeOnTicket( StoreCommentRequest $request, Ticket $ticket ) : RedirectResponse
        {
            $comment = $this->service->createOnTicket( array_merge( $request->validated(), [
                'commentable_id' => $ticket->id,
                'commentable_type' => \get_class( $ticket )
            ] ) );
            return redirect()->route( 'tickets.show', $ticket )
                ->with( 'success', __( 'comments.created_successfully' ) );
        }

        public function storeOnAnswer( StoreCommentRequest $request, Answer $answer ) : RedirectResponse
        {
            $comment = $this->service->createOnAnswer( array_merge( $request->validated(), [
                'commentable_id' => $answer->id,
                'commentable_type' => \get_class( $answer )
            ] ) );
            return redirect()->route( 'tickets.show', $answer->ticket )
                ->with( 'success', __( 'comments.created_successfully' ) );
        }


        public function destroy( Comment $comment ) : RedirectResponse
        {
            $this->authorize( 'delete', $comment );
            $this->service->delete( $comment->id );
            return back()->with( 'success', __( 'comments.deleted_successfully' ) );
        }
    }