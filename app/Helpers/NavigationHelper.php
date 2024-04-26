<?php

    namespace App\Helpers;

    class NavigationHelper
    {
        /**
         * Check if the navigation item is active.
         *
         * @param  string  $path
         * @param  array  $filters
         * @return bool
         */
        public static function isActiveNavigationItem( string $path, array $filters ) : bool
        {
            $request = request();

            // Default to not active
            $isActive = false;

            // Ensure the request instance is valid
            // Check if the request path matches
            if ( $request && $request->is( $path ) ) {
                // Safely retrieve and compare each filter value
                $isActive = true;
                $tableFilters = $request->query( 'tableFilters', [] );
                foreach ( $filters as $key => $value ) {
                    if ( ( $tableFilters[ $key ]['value'] ?? null ) !== $value ) {
                        $isActive = false;
                        break;
                    }
                }
            }

            return $isActive;
        }
    }