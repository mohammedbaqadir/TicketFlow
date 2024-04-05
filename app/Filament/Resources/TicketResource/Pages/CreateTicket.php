<?php

    namespace App\Filament\Resources\TicketResource\Pages;

    use App\Filament\Resources\TicketResource;
    use Filament\Actions;
    use Filament\Forms\Components\Textarea;
    use Filament\Forms\Components\TextInput;
    use Filament\Resources\Pages\CreateRecord;

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
            $data['priority'] = determinePriority( $data['title'], $data['description'] );
            $data['timeout_at'] = now()->addHours( determineTimeout( $data['priority'] ) );
            $data['assigned_to'] = null;

            return $data;
        }
    }