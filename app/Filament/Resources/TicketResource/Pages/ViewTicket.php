<?php

    namespace App\Filament\Resources\TicketResource\Pages;

    use App\Filament\Resources\TicketResource;
    use App\Livewire\ManageSolutions;
    use App\Livewire\TicketAttachments;
    use App\Livewire\TicketTimeline;
    use App\Models\Ticket;
    use App\Services\EventService;
    use Filament\Infolists\Components\TextEntry;
    use Filament\Infolists\Infolist;
    use Filament\Notifications\Notification;
    use Filament\Resources\Pages\ViewRecord;
    use Filament\Tables\Actions\Action;


    /**
     * Class ViewTicket
     *
     * Handles viewing of a single ticket with actions and widgets.
     */
    class ViewTicket extends ViewRecord
    {
        /**
         * @var string The associated resource for this page.
         */
        protected static string $resource = TicketResource::class;

        /**
         * Get the header widgets for the ticket view.
         * Disables actions if the ticket status is closed.
         *
         * @return array
         */
        protected function getHeaderWidgets() : array
        {
            if ( $this->record->status === 'closed' ) {
                return [];
            }

            return [
                // Add your header widgets here if any
            ];
        }

        /**
         * Define the infolist schema for displaying ticket details.
         *
         * @param  Infolist  $infolist
         * @return Infolist
         */
        public function infolist( Infolist $infolist ) : Infolist
        {
            return $infolist->schema( [
                TextEntry::make( 'id' )->label( 'Ticket ID' ),
                TextEntry::make( 'title' )->label( 'Title' ),
                TextEntry::make( 'description' )->label( 'Description' ),
                TextEntry::make( 'status' )->label( 'Status' ),
                TextEntry::make( 'priority' )->label( 'Priority' ),
                TextEntry::make( 'creator.name' )->label( 'Created By' ),
                TextEntry::make( 'assignee.name' )->label( 'Assigned To' ),
                TextEntry::make( 'created_at' )->label( 'Created At' )->dateTime(),
                TextEntry::make( 'updated_at' )->label( 'Updated At' )->dateTime(),
            ] );
        }

        /**
         * Get the actions for the ticket view page.
         * Includes actions for un-assigning and submitting solutions.
         *
         * @return array
         */
        protected function getActions() : array
        {
            return [
                Action::make( 'unassign' )
                    ->label( 'Un-Assign' )
                    ->visible( fn() => $this->record->isAssignee( auth()->user() ) )
                    ->action( function () {
                        $this->record->update( [
                            'assigned_to' => null,
                            'status' => 'open',
                        ] );
                        EventService::createEvent( $this->record, 'Ticket is un-assigned' );
                        EventService::createEvent( $this->record, 'Ticket status changed to `Open`.' );

                        Notification::make()
                            ->title( 'Success' )
                            ->body( 'You have been unassigned from the ticket.' )
                            ->success()
                            ->send();
                    } )
                    ->color( 'danger' )
                    ->requiresConfirmation(),

                Action::make( 'submitSolution' )
                    ->label( 'Submit a Solution' )
                    ->color( 'success' )
                    ->visible( fn( Ticket $record
                    ) => $this->record->isAssignee( auth()->user() ) && $record->status !== 'closed' )
                    ->modal( 'submitSolutionModal' )
                    ->modalFooterActions( [
                        Action::make( 'submit' )
                            ->label( 'Submit' )
                            ->action( 'submitSolution' ),
                    ] ),
            ];
        }

        /**
         * Get the content widgets for the ticket view page.
         *
         * @return array
         */
        protected function getContentWidgets() : array
        {
            return [
                TicketAttachments::class,
                TicketTimeline::class,
                ManageSolutions::class,
            ];
        }
    }