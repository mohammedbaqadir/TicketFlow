<?php
    declare( strict_types = 1 );

    namespace App\Filament\Resources\TicketResource\Pages;

    use App\Filament\Resources\TicketResource;
    use App\Jobs\DetermineTicketPriorityJob;
    use App\Models\Ticket;
    use Filament\Resources\Pages\CreateRecord;

    /**
     * Class CreateTicket
     *
     * Handles the creation of a ticket.
     */

    /**
     * @property Ticket $record
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
         * Mutate form data before creating a ticket.
         *
         * @param  array<string, mixed>  $data
         * @return array<string, mixed>
         */
        protected function mutateFormDataBeforeCreate( array $data ) : array
        {
            $data['status'] = 'open';
            $data['created_by'] = auth()->id();
            $data['assignee_id'] = null;
            return $data;
        }

        protected function afterCreate() : void
        {
            DetermineTicketPriorityJob::dispatch( $this->record );
        }

        protected function getRedirectUrl() : string
        {
            return self::getResource()::getUrl( 'index' );
        }


    }