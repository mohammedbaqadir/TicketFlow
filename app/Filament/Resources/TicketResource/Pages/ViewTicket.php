<?php

    namespace App\Filament\Resources\TicketResource\Pages;

    use App\Filament\Resources\TicketResource\Widgets\TicketTimelineWidget;
    use App\Models\Solution;
    use App\Services\EventService;
    use Filament\Actions\Action;
    use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
    use Filament\Forms\Components\Textarea;
    use Filament\Infolists\Components\ImageEntry;
    use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
    use Filament\Notifications\Notification;
    use App\Filament\Resources\TicketResource;
    use App\Models\Ticket;
    use App\Services\TicketService;
    use Filament\Infolists\Components\TextEntry;
    use Filament\Infolists\Infolist;
    use Filament\Resources\Pages\ViewRecord;


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
                TextEntry::make( 'requestor.name' )->label( 'Created By' ),
                TextEntry::make( 'assignee.name' )->label( 'Assigned To' ),
                TextEntry::make( 'created_at' )->label( 'Created At' )->dateTime(),
                TextEntry::make( 'updated_at' )->label( 'Updated At' )->dateTime(),
                SpatieMediaLibraryImageEntry::make( 'attachments' )
                    ->collection( 'ticket_attachments' )
                    ->label( 'Attachments' )
                    ->conversion( 'thumb' )
                    ->image(),
                TextEntry::make( 'attachments' )
                    ->collection( 'ticket_attachments' )
                    ->label( 'Other Attachments' )
                    ->displayUsing( fn( $attachments ) => $this->formatAttachments( $attachments ) )
                    ->columnSpan( 'full' ),

            ] );
        }

        /**
         * Format attachments for display
         *
         * @param $attachments
         * @return string
         */
        protected function formatAttachments( $attachments ) : string
        {
            return collect( $attachments )->filter( fn( $media ) => strpos( $media->mime_type, 'image' ) !== 0 )
                ->map( fn( $media
                ) => "<a href='{$media->getUrl()}' target='_blank' class='text-blue-500 underline'>{$media->file_name}</a>" )
                ->implode( ', ' );
        }

        /**
         * Get the header actions for the ticket view.
         * Disables actions if the ticket status is closed.
         *
         * @return array
         */
        protected function getHeaderActions() : array
        {
            $actions = [
                Action::make( 'unassign' )
                    ->label( 'Un-Assign' )
                    ->visible( fn() => $this->record->isAssignee( auth()->user() ) )
                    ->action( function () {
                        if ( $this->record instanceof Ticket ) {
                            ( new TicketService() )->unassignTicket( $this->record );

                            Notification::make()
                                ->title( 'Success' )
                                ->body( 'You have been unassigned from the ticket.' )
                                ->success()
                                ->send();
                        }
                    } )
                    ->color( 'danger' )
                    ->requiresConfirmation(),

                Action::make( 'submitSolution' )
                    ->label( 'Submit a Solution' )
                    ->color( 'success' )
                    ->visible( fn(
                    ) => $this->record->isAssignee( auth()->user() ) && $this->record->status !== 'closed' )
                    ->form( [
                        Textarea::make( 'content' )
                            ->label( 'Solution Content' )
                            ->required(),
                        SpatieMediaLibraryFileUpload::make( 'solution_attachments' )
                            ->collection( 'solution_attachments' )
                            ->multiple()
                            ->label( 'Attachments' )
                            ->preserveFilenames(),
                    ] )
                    ->action( function ( array $data ) {
                        $solution = Solution::create( [
                            'ticket_id' => $this->record->id,
                            'user_id' => auth()->id(),
                            'content' => $data['content'],
                        ] );

                        if ( isset( $data['solution_attachments'] ) ) {
                            foreach ( $data['solution_attachments'] as $file ) {
                                $solution->addMedia( $file )->toMediaCollection( 'solution_attachments' );
                            }
                        }

                        $this->record->update( [ 'status' => 'awaiting-acceptance' ] );
                        EventService::createEvent( $this->record->id, auth()->id(),
                            'Solution submitted by ' . auth()->user()->name );
                        EventService::createEvent( $this->record->id, auth()->id(),
                            'Ticket status changed to `Awaiting Acceptance`.' );

                        Notification::make()
                            ->title( 'Success' )
                            ->body( 'Solution submitted successfully.' )
                            ->success()
                            ->send();
                    } )
                    ->modalHeading( 'Submit a Solution' )
                    ->modalSubmitActionLabel( 'Submit' )
                    ->modalWidth( 'lg' ),
            ];


            return $actions;
        }


        /**
         * Get the footer widgets for the ticket view page.
         *
         * @return array
         */
        protected function getFooterWidgets() : array
        {
            return [
                TicketTimelineWidget::class,
            ];
        }
    }