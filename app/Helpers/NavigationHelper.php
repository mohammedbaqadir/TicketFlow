<?php

    namespace App\Helpers;


    use App\Models\Ticket;
    use App\Models\User;
    use Filament\Navigation\NavigationItem;

    class NavigationHelper
    {
        /**
         * Get navigation items based on the user's role.
         *
         * @return array
         */
        public static function getNavigationItemsForUser() : array
        {
            $user = auth()->user();

            // Check if the user is an agent and has ongoing tickets
            if ( AuthHelper::userHasRole( 'agent' ) ) {
                $hasOngoingTickets = Ticket::where( 'assigned_to', $user->id )
                    ->whereIn( 'status', [ 'in-progress', 'awaiting-acceptance' ] )
                    ->exists();

                // Redirect to assigned tickets if there are ongoing tickets
                if ( $hasOngoingTickets ) {
//                    return [
//                        'redirect' => url( 'app/tickets?tableFilters[assigned_to][value]=' . $user->id ),
//                    ];
                }
            }

            // Check if the user is an employee and return navigation items for tickets created by the employee
            if ( AuthHelper::userHasRole( 'employee' ) ) {
                return [
                    self::createEmployeeNavigationItem( 'Open', 'open', $user->id ),
                    self::createEmployeeNavigationItem( 'In-Progress', 'in-progress', $user->id ),
                    self::createEmployeeNavigationItem( 'Awaiting-Acceptance', 'awaiting-acceptance', $user->id ),
                    self::createEmployeeNavigationItem( 'Elevated', 'elevated', $user->id ),
                    self::createEmployeeNavigationItem( 'Closed', 'closed', $user->id ),
                ];
            }

            // Return the navigation items for different ticket statuses for admins and non-occupied agents
            return [
                self::createNavigationItem( 'Open', 'open' ),
                self::createNavigationItem( 'In-Progress', 'in-progress' ),
                self::createNavigationItem( 'Awaiting-Acceptance', 'awaiting-acceptance' ),
                self::createNavigationItem( 'Elevated', 'elevated' ),
                self::createNavigationItem( 'Closed', 'closed' ),
            ];
        }

        /**
         * Create a navigation item for a specific ticket status.
         *
         * @param  string  $label
         * @param  string  $status
         * @return NavigationItem
         */
        private static function createNavigationItem( string $label, string $status ) : NavigationItem
        {
            return NavigationItem::make( $label )
                ->url( '/app/tickets?tableFilters[status][value]=' . $status )
                ->icon( 'heroicon-o-ticket' )
                ->group( 'Tickets' )
                ->badge( Ticket::where( 'status', $status )->count() );
        }

        /**
         * Create a navigation item for an employee's ticket based on a specific status.
         *
         * @param  string  $label
         * @param  string  $status
         * @param  int  $userId
         * @return NavigationItem
         */
        private static function createEmployeeNavigationItem(
            string $label,
            string $status,
            int $userId
        ) : NavigationItem {
            return NavigationItem::make( $label )
                ->url( '/app/tickets?tableFilters[status][value]=' . $status . '&tableFilters[created_by][value]=' . $userId )
                ->icon( 'heroicon-o-ticket' )
                ->group( 'Tickets' )
                ->badge( Ticket::where( 'status', $status )->where( 'created_by', $userId )->count() );
        }

        /**
         * Check if a navigation item is active based on the current request path and filters.
         *
         * @param  string  $path
         * @param  array  $filters
         * @return bool
         */
        public static function isActiveNavigationItem( string $path, array $filters ) : bool
        {
            $request = request();
            $isActive = $request && $request->is( $path );

            if ( $isActive ) {
                $tableFilters = $request->query( 'tableFilters', [] );
                foreach ( $filters as $key => $value ) {
                    if ( ( $tableFilters[ $key ]['value'] ?? null ) !== $value ) {
                        return false;
                    }
                }
            }

            return $isActive;
        }
    }