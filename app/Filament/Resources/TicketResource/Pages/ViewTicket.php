<?php
    declare( strict_types = 1 );

    namespace App\Filament\Resources\TicketResource\Pages;

    use App\Actions\Answer\CreateAnswerAction;
    use App\Actions\Ticket\DeleteTicketAction;
    use App\Actions\Ticket\UnassignTicketAction;
    use App\Helpers\AuthHelper;
    use App\Livewire\SolutionEntry;
    use App\Models\User;
    use App\Repositories\AnswerRepository;
    use App\Repositories\TicketRepository;
    use App\Services\AnswerService;
    use App\Services\SolutionService;
    use Filament\Actions\Action;
    use Filament\Actions\EditAction;
    use Filament\Forms\Components\Hidden;
    use Filament\Forms\Components\MarkdownEditor;
    use Filament\Forms\Components\Select;
    use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
    use Filament\Forms\Components\Textarea;
    use Filament\Infolists\Components\Livewire;
    use Filament\Infolists\Components\RepeatableEntry;
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
                                        TextEntry::make( 'description' )->label( 'Description' )->markdown()->columnSpanFull(),
                                        TextEntry::make( 'formatted_status' )->label( 'Status' ),
                                        TextEntry::make( 'formatted_priority' )->label( 'Priority' ),
                                        TextEntry::make( 'requestor.name' )->label( 'Created By' ),
                                        TextEntry::make( 'assignee.name' )->label( 'Assigned To' ),
                                    ] ),
                                Tab::make( 'Answer' )
                                    ->icon( 'heroicon-o-document-text' )
                                    ->badge( $this->record->answers()->count() )
                                    ->schema( function () {
                                        return
                                            RepeatableEntry::make( 'answers' )
                                                ->schema( [
                                                    TextEntry::make( 'submitter.name' ),
                                                    TextEntry::make( 'title' ),
                                                    TextEntry::make( 'content' )->markdown()->columnSpanFull()
                                                ] );
                                    } )
                                    ->grow( true ),
                            ] )
                    ] ),
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
            return [
                Action::make( 'unassignTicket' )
                    ->label( 'Un-Assign Ticket' )
                    ->visible( fn( Ticket $record ) => $record->assignee_id !== null )
                    ->requiresConfirmation()
                    ->action( function ( array $data, Ticket $record ) {
                        ( new UnassignTicketAction() )->execute( $record );
                        return redirect()->route( 'filament.app.resources.tickets.view', $record );
                    } )
                    ->modalHeading( 'Un-Assign Ticket' )
                    ->modalSubmitActionLabel( 'Confirm' )
                    ->modalWidth( 'lg' ),
                Action::make( 'delete' )
                    ->label( 'Delete' )
                    ->action( function () {
                        ( new DeleteTicketAction() )->execute( $this->record );
                        Notification::make()
                            ->title( 'Deleted' )
                            ->body( 'The Ticket Has Been Deleted' )
                            ->success()
                            ->send();
                        return redirect()->route( 'filament.app.resources.tickets.index' );
                    } )
                    ->requiresConfirmation(),
                Action::make( 'submitSolution' )
                    ->label( 'Submit a Solution' )
                    ->color( 'success' )
                    ->visible( fn(
                    ) => AuthHelper::userIsAssignee( $this->record ) && $this->record->status !== 'resolved' )
                    ->form( [
                        MarkdownEditor::make( 'content' )
                            ->label( 'Answer' )
                            ->required()
                            ->disableToolbarButtons( [ 'attachFiles' ] ),
                        Hidden::make( 'ticket_id' )->default( $this->record->getKey() )
                    ] )
                    ->action( function ( array $data ) {
                        ( new CreateAnswerAction() )->execute( $data );
                        Notification::make()
                            ->title( 'Success' )
                            ->body( 'Answer submitted successfully.' )
                            ->success()
                            ->send();
                    } )
                    ->modalHeading( 'Submit an Answer' )
                    ->modalSubmitActionLabel( 'Submit' )
                    ->modalWidth( 'lg' ),
                EditAction::make( 'Edit' ),
            ];
        }

    }