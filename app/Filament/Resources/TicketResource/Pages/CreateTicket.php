<?php

    namespace App\Filament\Resources\TicketResource\Pages;

    use App\Filament\Resources\TicketResource;
    use App\Helpers\FormHelper;
    use App\Services\TicketService;
    use Filament\Actions;
    use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
    use Filament\Forms\Components\Textarea;
    use Filament\Forms\Components\TextInput;
    use Filament\Resources\Pages\CreateRecord;

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
         * Mutate form data before creating a ticket.
         *
         * @param  array  $data
         * @return array
         */
        protected function mutateFormDataBeforeCreate( array $data ) : array
        {
            $data['status'] = 'open';
            $data['created_by'] = auth()->id();
            $data['priority'] = $data['priority'] ?? TicketService::determinePriority( $data['title'],
                $data['description'] );
            $data['timeout_at'] = now()->addHours( TicketService::determineTimeout( $data['priority'] ) );
            $data['assigned_to'] = null;
            return $data;
        }

        protected function getRedirectUrl() : string
        {
            return self::getResource()::getUrl( 'index' );
        }



    }