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

        private CommentService $commentService;

        public function __construct( CommentService $commentService )
        {
            $this->commentService = $commentService;
            $this->authorizeResource( Comment::class, 'comment' );
        }

        public function storeOnTicket( StoreCommentRequest $request, Ticket $ticket ) : RedirectResponse
        {
            $comment = $this->commentService->createOnTicket( array_merge( $request->validated(), [
                'commentable_id' => $ticket->id,
                'commentable_type' => get_class( $ticket )
            ] ) );
            return redirect()->route( 'tickets.show', $ticket )
                ->with( 'success', __( 'comments.created_successfully' ) );
        }

        public function storeOnAnswer( StoreCommentRequest $request, Answer $answer ) : RedirectResponse
        {
            $comment = $this->commentService->createOnAnswer( array_merge( $request->validated(), [
                'commentable_id' => $answer->id,
                'commentable_type' => get_class( $answer )
            ] ) );
            return redirect()->route( 'tickets.show', $answer->ticket )
                ->with( 'success', __( 'comments.created_successfully' ) );
        }


        public function destroy( Comment $comment ) : RedirectResponse
        {
            $this->commentService->delete( $comment->id );
            return back()->with( 'success', __( 'comments.deleted_successfully' ) );
        }
    }