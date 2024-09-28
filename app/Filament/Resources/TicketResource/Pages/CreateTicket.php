<?php
    declare( strict_types = 1 );

    namespace App\Filament\Resources\TicketResource\Pages;

    use App\Filament\Resources\TicketResource;
    use App\Jobs\DetermineTicketPriorityJob;
    use App\Models\Ticket;
    use App\Repositories\TicketRepository;
    use App\Services\TicketService;
    use Filament\Resources\Pages\CreateRecord;
    use Illuminate\Support\Facades\DB;

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
         * Mutate form data before creating a ticket.
         *
         * @param  array  $data
         * @return array
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
            $ticket = $this->record;

            DetermineTicketPriorityJob::dispatch( $ticket );
        }

        protected function getRedirectUrl() : string
        {
            return self::getResource()::getUrl( 'index' );
        }


    }