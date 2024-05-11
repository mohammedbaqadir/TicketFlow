<?php

    namespace App\Filament\Resources\TicketResource\Pages;

    use App\Filament\Resources\TicketResource;
    use App\Helpers\TicketHelper;
    use App\Models\Ticket;
    use App\Services\EventService;
    use Filament\Actions;
    use Filament\Forms\Components\Textarea;
    use Filament\Forms\Components\TextInput;
    use Filament\Resources\Pages\CreateRecord;
    use Illuminate\Database\Eloquent\Model;

    /**
     * Class CreateTicket
     *
     * Handles the creation of a ticket.
     */
    class CreateTicket extends CreateRecord
    {
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
            $data['status'] = 'open';
            $data['created_by'] = auth()->id();
            $data['priority'] = TicketHelper::determinePriority( $data['title'], $data['description'] );
            $data['timeout_at'] = now()->addHours( TicketHelper::determineTimeout( $data['priority'] ) );
            $data['assigned_to'] = null;

            return $data;
        }


        /**
         * After creating a ticket, log the event.
         *
         * @param  Ticket  $record
         * @return void
         */
        protected function afterCreate( Model $record ) : void
        {
            // Ensure the record is an instance of Ticket
            if ( $record instanceof Ticket ) {
                EventService::createEvent( $record, 'Ticket was created' );
            }
        }


    }