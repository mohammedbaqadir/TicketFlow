<?php

    namespace App\Filament\Resources\TicketResource\Widgets;

    use App\Models\Solution;
    use App\Models\Ticket;
    use App\Services\EventService;
    use Filament\Notifications\Notification;
    use Filament\Widgets\Widget;
    use Illuminate\Support\Collection;

    class TicketTimelineWidget extends Widget
    {
        protected static string $view = 'filament.resources.ticket-resource.widgets.ticket-timeline-widget';
        protected int|string|array $columnSpan = 'full';

        public Ticket $record;
        public Collection $events;

        public $attachmentModalVisible = false;
        public $currentAttachment;

        /**
         * Initialize the widget with the given ticket.
         *
         * @param  Ticket  $ticket
         */
        public function mount( Ticket $record ) : void
        {
            $this->record = $record;
            $this->events = $record->events;
        }

        /**
         * Mark a solution as valid.
         *
         * @param  Solution  $solution
         * @return void
         */
        public function markSolutionAsValid( Solution $solution ) : void
        {
            $solution->update( [ 'resolved' => true ] );
            $solution->ticket->update( [ 'status' => 'closed' ] );
            EventService::createEvent( $solution->ticket->id, auth()->id(),
                'Solution successfully resolved the ticket, ticket closed' );
            Notification::make()
                ->title( 'Success' )
                ->body( 'Solution marked as valid and ticket closed.' )
                ->success()
                ->send();
        }

        /**
         * Mark a solution as invalid.
         *
         * @param  Solution  $solution
         * @return void
         */
        public function markSolutionAsInvalid( Solution $solution ) : void
        {
            $solution->update( [ 'resolved' => false ] );
            $solution->ticket->update( [ 'status' => 'in-progress' ] );
            EventService::createEvent( $solution->ticket->id, auth()->id(), 'Ticket status changed to `In-Progress`.' );

            Notification::make()
                ->title( 'Invalid' )
                ->body( 'Solution marked as invalid.' )
                ->info()
                ->send();
        }

        /**
         * Undo the marking of a solution.
         *
         * @param  Solution  $solution
         * @return void
         */
        public function undoMarking( Solution $solution ) : void
        {
            $solution->update( [ 'resolved' => null ] );
        }

        /**
         * Open the attachment modal.
         *
         * @param  string  $attachmentUrl
         * @return void
         */
        public function openAttachment( string $attachmentUrl ) : void
        {
            $this->currentAttachment = $attachmentUrl;
            $this->attachmentModalVisible = true;
        }


    }