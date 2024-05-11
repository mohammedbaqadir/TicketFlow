<?php

    namespace App\Filament\Resources;

    use App\Filament\Resources\TicketResource\Pages\CreateTicket;
    use App\Filament\Resources\TicketResource\Pages\EditTicket;
    use App\Filament\Resources\TicketResource\Pages\ListTickets;
    use App\Filament\Resources\TicketResource\Pages\ViewTicket;
    use App\Filament\Resources\TicketResource\RelationManagers\AssigneeRelationManager;
    use App\Filament\Resources\TicketResource\RelationManagers\RequestorRelationManager;
    use App\Helpers\AuthHelper;
    use App\Helpers\NavigationHelper;
    use App\Livewire\AssignTicketModal;
    use App\Models\Ticket;
    use App\Models\User;
    use App\Services\EventService;
    use App\Traits\HasCustomRecordUrl;
    use Exception;
    use Filament\Forms\Components\Placeholder;
    use Filament\Forms\Components\Select;
    use Filament\Navigation\NavigationItem;
    use Filament\Resources\Resource;
    use Filament\Tables\Actions\Action;
    use Filament\Tables\Actions\BulkActionGroup;
    use Filament\Tables\Actions\DeleteBulkAction;
    use Filament\Tables\Actions\EditAction;
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
            if ( AuthHelper::userHasRole( 'agent' ) ) {
                $hasOngoingTickets = Ticket::where( 'assigned_to', auth()->user()->id )
                    ->whereIn( 'status', [ 'in-progress', 'awaiting-acceptance' ] )
                    ->exists();

                if ( $hasOngoingTickets ) {
                    app( 'redirect' )->to( '/tickets?tableFilters[assigned_to][value]=' . auth()->user()->id )->send();
                }
            }

            return [
                NavigationItem::make( 'Open' )
                    ->url( '/app/tickets?tableFilters[status][value]=open' )
                    ->icon( 'heroicon-o-ticket' )
                    ->group( 'Tickets' )
                    ->isActiveWhen( fn() => NavigationHelper::isActiveNavigationItem( 'app/tickets',
                        [ 'status' => 'open' ] ) )
                    ->badge( fn() => Ticket::isOpen()->count() ),

                NavigationItem::make( 'In-Progress' )
                    ->url( '/app/tickets?tableFilters[status][value]=in-progress' )
                    ->icon( 'heroicon-o-ticket' )
                    ->group( 'Tickets' )
                    ->isActiveWhen( fn() => NavigationHelper::isActiveNavigationItem( 'app/tickets',
                        [ 'status' => 'in-progress' ] ) )
                    ->badge( fn() => Ticket::isInProgress()->count() ),

                NavigationItem::make( 'Awaiting-Acceptance' )
                    ->url( '/app/tickets?tableFilters[status][value]=awaiting-acceptance' )
                    ->icon( 'heroicon-o-ticket' )
                    ->group( 'Tickets' )
                    ->isActiveWhen( fn() => NavigationHelper::isActiveNavigationItem( 'app/tickets',
                        [ 'status' => 'awaiting-acceptance' ] ) )
                    ->badge( fn() => Ticket::isAwaitingAcceptance()->count() ),

                NavigationItem::make( 'Elevated' )
                    ->url( '/app/tickets?tableFilters[status][value]=elevated' )
                    ->icon( 'heroicon-o-ticket' )
                    ->group( 'Tickets' )
                    ->isActiveWhen( fn() => NavigationHelper::isActiveNavigationItem( 'app/tickets',
                        [ 'status' => 'elevated' ] ) )
                    ->badge( fn() => Ticket::isElevated()->count() ),

                NavigationItem::make( 'Closed' )
                    ->url( '/app/tickets?tableFilters[status][value]=closed' )
                    ->icon( 'heroicon-o-ticket' )
                    ->group( 'Tickets' )
                    ->isActiveWhen( fn() => NavigationHelper::isActiveNavigationItem( 'app/tickets',
                        [ 'status' => 'closed' ] ) )
                    ->badge( fn() => Ticket::isClosed()->count() ),
            ];
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
            return $table
                ->columns( [
                    TextColumn::make( 'id' )->sortable(),
                    TextColumn::make( 'title' )->sortable()->searchable(),
                    TextColumn::make( 'description' )->sortable()->searchable(),
                    TextColumn::make( 'status' )->sortable()->searchable(),
                    TextColumn::make( 'priority' )->sortable()->searchable(),
                    TextColumn::make( 'creator.name' )->label( 'Created By' )->sortable()->searchable(),
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
                        // Show the action only if the ticket is not assigned
                        ->visible( fn( Ticket $record ) => $record->assigned_to === null )
                        ->form( function ( Ticket $record ) : array {
                            if ( AuthHelper::userHasRole( 'agent' ) ) {
                                return [
                                    // Confirmation for agent to assign the ticket to themselves
                                    Placeholder::make( 'confirm' )
                                        ->content( 'Assign this ticket to yourself?' ),
                                ];
                            }

                            if ( AuthHelper::userHasRole( 'admin' ) ) {
                                return [
                                    // Options for admin to assign the ticket to themselves or another agent
                                    Select::make( 'action' )
                                        ->label( 'Action' )
                                        ->options( [
                                            'assign_to_self' => 'Assign to Myself',
                                            'assign_to_agent' => 'Assign to Agent',
                                        ] )
                                        ->reactive()
                                        ->required(),
                                    Select::make( 'agent_id' )
                                        ->label( 'Assign to Agent' )
                                        ->options( User::isAgent()->pluck( 'name', 'id' ) )
                                        ->visible( fn( $get ) => $get( 'action' ) === 'assign_to_agent' )
                                        ->required( fn( $get ) => $get( 'action' ) === 'assign_to_agent' ),
                                ];
                            }

                            return [];
                        } )
                        ->action( function ( array $data, Ticket $record ) {
                            $ticket = Ticket::findOrFail( $record->id );

                            if ( AuthHelper::userHasRole( 'agent' ) ) {
                                // Assign ticket to the agent themselves and update status to in-progress
                                $ticket->update( [
                                    'assigned_to' => auth()->user()->id,
                                    'status' => 'in-progress'
                                ] );
                                EventService::createEvent( $ticket,
                                    'Ticket assigned to ' . auth()->user()->name . '.' );
                                EventService::createEvent( $ticket,
                                    'Ticket status changed to `In-Progress`.' );
                            } elseif ( AuthHelper::userHasRole( 'admin' ) ) {
                                if ( $data['action'] === 'assign_to_self' ) {
                                    // Assign ticket to the admin themselves and update status to in-progress
                                    $ticket->update( [
                                        'assigned_to' => auth()->user()->id,
                                        'status' => 'in-progress'
                                    ] );
                                    EventService::createEvent( $ticket,
                                        'Ticket assigned to ' . auth()->user()->name . '.' );
                                    EventService::createEvent( $ticket,
                                        'Ticket status changed to `In-Progress`.' );
                                } elseif ( $data['action'] === 'assign_to_agent' ) {
                                    // Assign ticket to the selected agent and update status to in-progress
                                    $ticket->update( [
                                        'assigned_to' => $data['agent_id'],
                                        'status' => 'in-progress'
                                    ] );
                                    EventService::createEvent( $ticket,
                                        'Ticket assigned to ' . User::firstWhere( 'id',
                                            $data['agent_id'] )->name ) . ' by Admin.';
                                    EventService::createEvent( $ticket,
                                        'Ticket status changed to `In-Progress`.' );
                                }
                            }

                            return redirect()->route( 'filament.app.resources.tickets.view', $ticket );
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
                ] );
        }

        /**
         * Get the relation managers for the resource.
         *
         * @return array
         */
        public static function getRelations() : array
        {
            return [
                RequestorRelationManager::class,
                AssigneeRelationManager::class,
            ];
        }

        /**
         * Get the pages for the resource.
         *
         * @return array
         */
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