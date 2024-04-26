<?php

    namespace App\Traits;

    use App\Helpers\AuthHelper;
    use Closure;
    use Filament\Tables\Contracts\HasTable;

    /**
     * Trait HasCustomRecordUrl
     *
     * Provides a custom URL for table records, including logic for assigning tickets.
     *
     * @package App\Traits
     */
    trait HasCustomRecordUrl
    {
        /**
         * Get the URL to view a specific table record, or trigger a JavaScript event to show a modal if the user is not assigned.
         *
         * @return \Closure|null
         */
        public function getTableRecordUrlUsing() : ?\Closure
        {
            return function ( $record ) {
                $user = auth()->user();

                // Check if the user is the assignee of the record or has an admin role
                if ( $record->isAssignee( $user ) || AuthHelper::userHasRole( 'admin' ) ) {
                    return route( 'filament.app.resources.tickets.view', $record );
                }

                // Set URL to "#"
                return '#';
            };
        }
    }