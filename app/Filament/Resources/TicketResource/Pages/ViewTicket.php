<?php
    declare( strict_types = 1 );

    namespace App\Filament\Resources\TicketResource\Pages;

    use App\Livewire\SolutionEntry;
    use App\Models\User;
    use App\Services\SolutionService;
    use Filament\Actions\Action;
    use Filament\Actions\EditAction;
    use Filament\Forms\Components\Select;
    use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
    use Filament\Forms\Components\Textarea;
    use Filament\Infolists\Components\Livewire;
    use Filament\Infolists\Components\Section;
    use Filament\Infolists\Components\Split;
    use Filament\Infolists\Components\Tabs;
    use Filament\Infolists\Components\Tabs\Tab;
    use Filament\Notifications\Notification;
    use App\Filament\Resources\TicketResource;
    use App\Models\Ticket;
    use App\Services\TicketService;
    use Filament\Infolists\Components\TextEntry;
    use Filament\Infolists\Infolist;
    use Filament\Resources\Pages\ViewRecord;
    use Illuminate\Contracts\Support\Htmlable;
    use Njxqlus\Filament\Components\Infolists\LightboxSpatieMediaLibraryImageEntry;


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
         * @return string|null
         */
        public function getHeading() : string|Htmlable
        {
            return $this->record->title;
        }


        /**
         * Define the infolist schema for displaying ticket details.
         *
         * @param  Infolist  $infolist
         * @return Infolist
         */
        public function infolist( Infolist $infolist ) : Infolist
        {
            return $infolist->schema( [
                Split::make( [
                    Section::make( [
                        Tabs::make( 'Tabs' )
                            ->tabs( [
                                Tab::make( 'Details' )
                                    ->icon( 'heroicon-o-question-mark-circle' )
                                    ->schema( [
                                        TextEntry::make( 'description' )->label( 'Description' ),
                                        TextEntry::make( 'formatted_status' )->label( 'Status' ),
                                        TextEntry::make( 'formatted_priority' )->label( 'Priority' ),
                                        TextEntry::make( 'requestor.name' )->label( 'Created By' ),
                                        TextEntry::make( 'assignee.name' )->label( 'Assigned To' ),
                                    ] ),
                                Tab::make( 'Attachments' )
                                    ->icon( 'heroicon-o-photo' )
                                    ->schema( [
                                        LightboxSpatieMediaLibraryImageEntry::make( 'attachments' )
                                            ->collection( 'ticket_attachments' )
                                            ->label( 'Attachments' )->conversion( 'thumb' )->columnSpan( 'full' )
                                    ] ),
                                Tab::make( 'Solutions' )
                                    ->icon( 'heroicon-o-document-text' )
                                    ->badge( $this->record->solutions()->count() )
                                    ->schema( function () {
                                        $solutions = $this->record->solutions()->get()->toArray();
                                        $solution_entries = [];
                                        foreach ( $solutions as $solution ) {
                                            $solution_entries[] = Livewire::make( SolutionEntry::class,
                                                [ 'solution' => $solution ] );
                                        }
                                        return $solution_entries;
                                    } ),
                            ] )
                    ] )->grow( true ),
                    Section::make( [
                        TextEntry::make( 'id' )->label( 'Ticket ID' ),
                        TextEntry::make( 'created_at' )->label( 'Created At' )->dateTime(),
                        TextEntry::make( 'updated_at' )->label( 'Updated At' )->dateTime(),
                    ] )->grow( false )
                ] )->from( 'md' )->columnSpanFull()
            ] );
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
                Action::make( 'delete' )
                    ->label( 'Delete' )
                    ->action( function () {
                        if ( $this->record instanceof Ticket ) {
                            $ticket = Ticket::find( $this->record->getKey() );
                            if ( $ticket ) {
                                $ticket->delete();
                                Notification::make()
                                    ->title( 'Deleted' )
                                    ->body( 'The Ticket Has Been Deleted' )
                                    ->success()
                                    ->send();
                                return redirect()->route( 'filament.app.resources.tickets.index' );
                            } else {
                                Notification::make()
                                    ->title( 'Error' )
                                    ->body( 'The Ticket no longer exists' )
                                    ->danger()
                                    ->send();
                            }
                        } else {
                            Notification::make()
                                ->title( 'Error' )
                                ->body( 'Invalid Ticket' )
                                ->danger()
                                ->send();
                        }
                    } )
                    ->color( 'danger' )
                    ->requiresConfirmation(),

                Action::make( 'assign-another' )
                    ->label( 'Assign a Different Agent' )
                    ->visible( fn() => !( $this->record->isAssignee( auth()->user() ) ))
                    ->form( function ( Ticket $record ) : array {
                        $formSchema = [];
                        $formSchema[] = Select::make( 'action' )
                            ->label( 'Action' )
                            ->options( [
                                'assign_to_self' => 'Assign to Myself',
                                'assign_to_agent' => 'Assign to Agent',
                            ] )
                            ->reactive()
                            ->required();
                        $formSchema[] = Select::make( 'agent_id' )
                            ->label( 'Assign to Agent' )
                            ->options( User::isAgent()->pluck( 'name', 'id' ) )
                            ->visible( fn( $get ) => $get( 'action' ) === 'assign_to_agent' )
                            ->required( fn( $get ) => $get( 'action' ) === 'assign_to_agent' );
                        return $formSchema;
                    } )
                    ->action( function ( array $data, Ticket $record) {
                        ( new TicketService() )->unassignTicket( $record );
                        if ( $data['action'] === 'assign_to_self' ) {
                            ( new TicketService() )->assignTicket( $record, auth()->user() );
                        } elseif ( $data['action'] === 'assign_to_agent' ) {
                            $agent = User::findOrFail( $data['agent_id'] );
                            ( new TicketService() )->assignTicket( $record, $agent, 'Admin' );
                        }

                        return redirect()->route( 'filament.app.resources.tickets.view', $record );
                    } )
                    ->color( 'danger' )
                    ->modalHeading('Assign Ticket' )
                    ->modalSubmitActionLabel( 'Assign' )
                    ->modalWidth( 'lg' ),
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
                    ] )
                    ->action( function ( array $data ) {
                        ( new SolutionService() )->submitSolution( $this->record, auth()->user(), $data );
                        Notification::make()
                            ->title( 'Success' )
                            ->body( 'Solution submitted successfully.' )
                            ->success()
                            ->send();
                    } )
                    ->modalHeading( 'Submit a Solution' )
                    ->modalSubmitActionLabel( 'Submit' )
                    ->modalWidth( 'lg' ),
                EditAction::make( 'Edit' ),
            ];


            return $actions;
        }

    }