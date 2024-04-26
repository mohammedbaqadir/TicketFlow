<?php

    namespace App\Filament\Resources\TicketResource\Pages;

    use App\Filament\Resources\TicketResource;
    use App\Helpers\AuthHelper;
    use App\Helpers\TicketHelper;
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
            $isEmployee = AuthHelper::userHasRole( 'employee' );
            $isAgent = AuthHelper::userHasRole( 'agent' );

            return [
                TextInput::make( 'title' )
                    ->maxLength( 255 )
                    ->visible( $isEmployee ),
                Textarea::make( 'description' )
                    ->visible( $isEmployee ),
                Select::make( 'priority' )
                    ->options( [
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ] )
                    ->visible( $isAgent ),
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
            if ( AuthHelper::userHasRole( 'agent' ) ) {
                $data['timeout_at'] = now()->addHours( TicketHelper::determineTimeout( $data['priority'] ) );
            } else {
                $data['priority'] = TicketHelper::determinePriority( $data['title'], $data['description'] );
                $data['timeout_at'] = now()->addHours( TicketHelper::determineTimeout( $data['priority'] ) );
            }

            return $data;
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