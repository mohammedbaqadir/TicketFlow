<?php
    declare( strict_types = 1 );

    namespace App\Helpers;

    use App\Models\Answer;
    use App\Models\Comment;
    use App\Models\Ticket;

    class AuthHelper
    {
        /**
         * Check if the authenticated user has a given role.
         *
         * @param  string  $role
         * @return bool
         */
        public static function userHasRole( string $role ) : bool
        {
            $user = auth()->user();

            return $user !== null && $user->role === $role;
        }

        public static function userIsRequestor( Ticket $ticket ) : bool
        {
            $user = auth()->user();
            return $user !== null && $ticket->requestor_id === $user->id;
        }

        public static function userIsAssignee( Ticket $ticket ) : bool
        {
            $user = auth()->user();
            return $user !== null && $ticket->assignee_id === $user->id;
        }

        public static function userIsSubmitter( Answer $answer ) : bool
        {
            $user = auth()->user();
            return $user !== null && $answer->submitter_id === $user->id;
        }

        public static function userIsCommenter( Comment $comment ) : bool
        {
            $user = auth()->user();
            return $user !== null && $comment->commenter_id === $user->id;
        }


    }