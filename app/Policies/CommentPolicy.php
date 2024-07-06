<?php
    declare( strict_types = 1 );

    namespace App\Policies;

    use App\Models\Comment;
    use App\Models\User;

    class CommentPolicy
    {

        public function delete( User $user, Comment $comment ) : bool
        {
            return $comment->isCommenter( $user );
        }


    }