<?php
    declare( strict_types = 1 );

    namespace App\Filament\Resources\TicketResource\Pages;

    use App\Filament\Resources\TicketResource;
    use App\Jobs\DetermineTicketPriorityJob;
    use App\Models\Ticket;
    use App\Repositories\TicketRepository;
    use App\Services\TicketService;
    use Filament\Actions\DeleteAction;
    use Filament\Forms\Components\MarkdownEditor;
    use Filament\Forms\Components\Select;
    use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
    use Filament\Forms\Components\Textarea;
    use Filament\Forms\Components\TextInput;
    use Filament\Forms\Form;
    use Filament\Resources\Pages\EditRecord;

    /**
     * Class EditTicket
     *
     * Handles the editing of a ticket.
     */
    class EditTicket extends EditRecord
    {
        /**
         * The associated resource.
         *
         * @var string
         */
        protected static string $resource = TicketResource::class;

        function form( Form $form ) : Form
        {
            return $form->schema( [
                TextInput::make( 'title' )
                    ->required()
                    ->maxLength( 255 ),
                MarkdownEditor::make( 'description' )
                    ->required()
                    ->disableToolbarButtons( [ 'attachFiles' ] ),
            ] );
        }

        protected function afterSave() : void
        {
            $ticket = $this->record;

            DetermineTicketPriorityJob::dispatch( $ticket );
        }


        /**
         * Get the header actions for the edit page.
         *
         * @return array
         */
        protected function getHeaderActions() : array
        {
            return [
                DeleteAction::make(),
            ];
        }
    }