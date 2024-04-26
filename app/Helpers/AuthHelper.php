<?php

    namespace App\Helpers;

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
    }