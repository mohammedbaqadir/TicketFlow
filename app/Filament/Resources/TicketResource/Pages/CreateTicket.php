<?php
    declare( strict_types = 1 );

    namespace App\Filament\Resources\TicketResource\Pages;

    use App\Filament\Resources\TicketResource;
    use App\Models\Ticket;
    use App\Repositories\TicketRepository;
    use App\Services\TicketService;
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
         * Mutate form data before creating a ticket.
         *
         * @param  array  $data
         * @return array
         */
        protected function mutateFormDataBeforeCreate( array $data ) : array
        {
            $ticketService = new TicketService( new TicketRepository( new Ticket()));

            $data['status'] = 'open';
            $data['created_by'] = auth()->id();
            $data['priority'] = $data['priority'] ?? $ticketService->determinePriority( $data['title'],
                $data['description'] );
            $data['timeout_at'] = now()->addHours( $ticketService->determineTimeout( $data['priority'] ) );
            $data['assignee_id'] = null;
            return $data;
        }

        protected function getRedirectUrl() : string
        {
            return self::getResource()::getUrl( 'index' );
        }


    }