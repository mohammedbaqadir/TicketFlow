<?php

    namespace App\Filament\Resources;

    use App\Filament\Resources\TicketResource\Pages\CreateTicket;
    use App\Filament\Resources\TicketResource\Pages\EditTicket;
    use App\Filament\Resources\TicketResource\Pages\ListTickets;
    use App\Filament\Resources\TicketResource\Pages\ViewTicket;
    use App\Filament\Resources\TicketResource\Pages\ViewTicketActivities;
    use App\Filament\Resources\TicketResource\RelationManagers\AssigneeRelationManager;
    use App\Filament\Resources\TicketResource\RelationManagers\RequestorRelationManager;
    use App\Helpers\AuthHelper;
    use App\Helpers\NavigationHelper;
    use App\Models\Ticket;
    use App\Models\User;
    use App\Services\TicketService;
    use App\Traits\HasCustomRecordUrl;
    use Exception;
    use Filament\Forms\Components\Placeholder;
    use Filament\Forms\Components\Select;
    use Filament\Navigation\NavigationItem;
    use Filament\Resources\Resource;
    use Filament\Tables\Actions\Action;
    use Filament\Tables\Actions\BulkActionGroup;
    use Filament\Tables\Actions\DeleteBulkAction;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Filters\SelectFilter;
    use Filament\Tables\Table;

    /**
     * Class TicketResource
     *
     * Manages the Filament resource for tickets, including pages, relations, and custom navigation items.
     *
     * @package App\Filament\Resources
     */

    /**
     * Class TicketResource
     *
     * Manages the Filament resource for tickets, including pages, relations, and custom navigation items.
     *
     * @package App\Filament\Resources
     */
    class TicketResource extends Resource
    {
        use HasCustomRecordUrl;

        /**
         * The associated model for the resource.
         *
         * @var string|null
         */
        protected static ?string $model = Ticket::class;

        /**
         * The icon for the navigation.
         *
         * @var string|null
         */
        protected static ?string $navigationIcon = 'heroicon-o-ticket';

        /**
         * Get the navigation items for the resource.
         *
         * @return array
         */
        public static function getNavigationItems() : array
        {
            $navigationItems = NavigationHelper::getNavigationItemsForUser();

            // Check for redirect in the returned navigation items
            if ( isset( $navigationItems['redirect'] ) ) {
                // Perform redirect
                app( 'redirect' )->to( $navigationItems['redirect'] )->send();
            }

            // Filter out any non-navigation item arrays
            return array_filter( $navigationItems, fn( $item ) => $item instanceof NavigationItem );
        }

        /**
         * Define the table for the resource.
         *
         * @param  Table  $table
         * @return Table
         * @throws Exception
         */
        public static function table( Table $table ) : Table
        {
            $user = auth()->user();
            return $table
                ->columns( [
                    TextColumn::make( 'id' )->sortable(),
                    TextColumn::make( 'title' )->sortable()->searchable(),
                    TextColumn::make( 'description' )->sortable()->searchable(),
                    TextColumn::make( 'status' )->sortable()->searchable(),
                    TextColumn::make( 'priority' )->sortable()->searchable(),
                    TextColumn::make( 'requestor.name' )->label( 'Created By' )->sortable()->searchable(),
                    TextColumn::make( 'assignee.name' )->label( 'Assigned To' )->sortable()->searchable(),
                    TextColumn::make( 'created_at' )->sortable()->dateTime(),
                    TextColumn::make( 'timeout_at' )->sortable()->dateTime(),
                ] )
                ->recordUrl( ( new static() )->getTableRecordUrlUsing() )
                ->filters( [
                    SelectFilter::make( 'status' )
                        ->options( [
                            'open' => 'Open',
                            'in-progress' => 'In Progress',
                            'awaiting-acceptance' => 'Awaiting Acceptance',
                            'elevated' => 'Elevated',
                            'closed' => 'Closed',
                        ] ),
                    SelectFilter::make( 'priority' )
                        ->options( [
                            'low' => 'Low',
                            'medium' => 'Medium',
                            'high' => 'High',
                        ] ),
                ] )
                ->actions( [
                    Action::make( 'assignTicket' )
                        ->label( 'Assign Ticket' )
                        ->visible( fn( Ticket $record ) => $record->assigned_to === null )
                        ->form( function ( Ticket $record ) : array {
                            $formSchema = [];

                            if ( AuthHelper::userHasRole( 'agent' ) ) {
                                $formSchema[] = Placeholder::make( 'confirm' )
                                    ->content( 'Assign this ticket to yourself?' );
                            }

                            if ( AuthHelper::userHasRole( 'admin' ) ) {
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
                            }

                            return $formSchema;
                        } )
                        ->action( function ( array $data, Ticket $record ) {
                            ( new TicketService() )->assignTicket( $record, $data );

                            return redirect()->route( 'filament.app.resources.tickets.view', $record );
                        } )
                        ->modalHeading( fn(
                        ) => AuthHelper::userHasRole( 'agent' ) ? 'Confirm Assignment' : 'Assign Ticket' )
                        ->modalSubmitActionLabel( 'Confirm' )
                        ->modalWidth( 'lg' ),

                ] )
                ->bulkActions( [
                    BulkActionGroup::make( [
                        DeleteBulkAction::make(),
                    ] ),
                ] )
                ->deferLoading()
                ->striped();
        }

        public static function getRelations() : array
        {
            return [
//                RequestorRelationManager::class,
//                AssigneeRelationManager::class,
            ];
        }

        public static function getPages() : array
        {
            return [
                'index' => ListTickets::route( '/' ),
                'create' => CreateTicket::route( '/create' ),
                'edit' => EditTicket::route( '/{record}/edit' ),
                'view' => ViewTicket::route( '/{record}' ),
            ];
        }
    }