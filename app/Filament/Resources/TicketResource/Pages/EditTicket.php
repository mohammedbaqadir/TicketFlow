<?php

    namespace App\Filament\Resources\TicketResource\Pages;

    use App\Filament\Resources\TicketResource;
    use App\Helpers\AuthHelper;
    use App\Helpers\FormHelper;
    use App\Helpers\TicketHelper;
    use App\Services\TicketService;
    use Filament\Actions;
    use Filament\Actions\DeleteAction;
    use Filament\Forms\Components\Select;
    use Filament\Forms\Components\Textarea;
    use Filament\Forms\Components\TextInput;
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

        /**
         * Get the form schema for editing a ticket.
         *
         * @return array
         */
        protected function getFormSchema() : array
        {
            return [
                TextInput::make( 'title' )
                    ->maxLength( 255 )
                    ->visible( AuthHelper::userHasRole( 'employee' ) ),
                Textarea::make( 'description' )
                    ->visible( AuthHelper::userHasRole( 'employee' ) ),
                Select::make( 'priority' )
                    ->options( [
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ] )
                    ->visible( AuthHelper::userHasRole( 'agent' ) ),
            ];
        }

        /**
         * Mutate form data before saving a ticket.
         *
         * @param  array  $data
         * @return array
         */
        protected function mutateFormDataBeforeSave( array $data ) : array
        {
            return FormHelper::handleEditTicketFormData( $data );
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