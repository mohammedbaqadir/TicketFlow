<?php

    namespace App\Filament\Resources\TicketResource\Pages;

    use App\Filament\Resources\TicketResource;
    use App\Helpers\FormHelper;
    use App\Models\Ticket;
    use App\Services\EventService;
    use App\Services\TicketService;
    use Filament\Actions;
    use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
    use Filament\Forms\Components\Textarea;
    use Filament\Forms\Components\TextInput;
    use Filament\Forms\Concerns\InteractsWithForms;
    use Filament\Forms\Contracts\HasForms;
    use Filament\Resources\Pages\CreateRecord;
    use Illuminate\Database\Eloquent\Model;

    /**
     * Class CreateTicket
     *
     * Handles the creation of a ticket.
     */
    class CreateTicket extends CreateRecord {

        /**
         * The associated resource.
         *
         * @var string
         */
        protected static string $resource = TicketResource::class;

        /**
         * Get the form schema for creating a ticket.
         *
         * @return array
         */
        protected function getFormSchema() : array
        {
            return [
                TextInput::make( 'title' )
                    ->required()
                    ->maxLength( 255 ),
                Textarea::make( 'description' )
                    ->required(),
                SpatieMediaLibraryFileUpload::make( 'attachments' )
                    ->collection( 'ticket_attachments' )
                    ->multiple()
                    ->label( 'Attachments' )
                    ->preserveFilenames(),
            ];
        }

        /**
         * Mutate form data before creating a ticket.
         *
         * @param  array  $data
         * @return array
         */
        protected function mutateFormDataBeforeCreate( array $data ) : array
        {
            return FormHelper::handleCreateTicketFormData( $data );
        }


        /**
         * Get the create action.
         *
         * @return array
         */
        protected function getActions() : array
        {
            return [
                Actions\CreateAction::make()
                    ->after( function () {
                        // Runs after the form fields are saved to the database.
                        if ( $this->record instanceof Ticket ) {
                            EventService::createEvent( $this->record->id, auth()->id(), 'Ticket was created' );
                        }
                    } )
            ];
        }


    }