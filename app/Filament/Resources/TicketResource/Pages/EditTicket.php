<?php
    declare( strict_types = 1 );

    namespace App\Filament\Resources\TicketResource\Pages;

    use App\Filament\Resources\TicketResource;
    use App\Models\Ticket;
    use App\Services\TicketService;
    use Filament\Actions\DeleteAction;
    use Filament\Forms\Components\Select;
    use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
    use Filament\Forms\Components\Textarea;
    use Filament\Forms\Components\TextInput;
    use Filament\Forms\Form;
    use Filament\Resources\Pages\EditRecord;

    /**
     * Class EditTicket
     *
     * Handles the editing of a ticket.
     */
    class EditTicket extends EditRecord
    {
        /**
         * The associated resource.
         *
         * @var string
         */
        protected static string $resource = TicketResource::class;

        function form( Form $form ) : Form
        {
            return $form->schema( [
                TextInput::make( 'title' )
                    ->maxLength( 255 ),
                Textarea::make( 'description' ),
                SpatieMediaLibraryFileUpload::make( 'attachments' )
                    ->collection( 'ticket_attachments' )
                    ->multiple()
                    ->label( 'Attachments' )
                    ->acceptedFileTypes( [ 'image/jpeg', 'image/png', 'image/gif' ] ),
                Select::make( 'priority' )
                    ->options( Ticket::getFormattedPriorityMappings() )
            ] );
        }

        /**
         * Mutate form data before saving a ticket.
         *
         * @param  array  $data
         * @return array
         */
        protected function mutateFormDataBeforeSave( array $data ) : array
        {
            $ticketService = app( TicketService::class );

            $data['priority'] = $ticketService->determinePriority( $data['title'], $data['description'] );
            $data['timeout_at'] = now()->addHours( $ticketService->determineTimeout( $data['priority'] ) );
            return $data;
        }

        /**
         * Get the header actions for the edit page.
         *
         * @return array
         */
        protected function getHeaderActions() : array
        {
            return [
                DeleteAction::make(),
            ];
        }
    }