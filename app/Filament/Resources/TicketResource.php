<?php
    declare( strict_types = 1 );

    namespace App\Filament\Resources;

    use App\Filament\Resources\TicketResource\Pages\CreateTicket;
    use App\Filament\Resources\TicketResource\Pages\EditTicket;
    use App\Filament\Resources\TicketResource\Pages\ListTickets;
    use App\Filament\Resources\TicketResource\Pages\ViewTicket;
    use App\Models\Ticket;
    use App\Models\User;
    use App\Services\TicketService;
    use Exception;
    use Filament\Forms\Components\Select;
    use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
    use Filament\Forms\Components\Textarea;
    use Filament\Forms\Components\TextInput;
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
    class TicketResource extends Resource
    {

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
            return self::getTicketsNavigationItems();
        }

        public static function form( $form ) : \Filament\Forms\Form
        {
            return $form
                ->schema( [
                    TextInput::make( 'title' )
                        ->required()
                        ->maxLength( 255 ),
                    Textarea::make( 'description' )
                        ->required(),
                    Select::make( 'priority' )
                        ->options( Ticket::getFormattedPriorityMappings() ),
                    SpatieMediaLibraryFileUpload::make( 'attachments' )
                        ->collection( 'ticket_attachments' )
                        ->multiple()
                        ->label( 'Attachments' )
                ] );
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
                    TextColumn::make( 'title' )->label( 'Title')->sortable()->searchable(),
                    TextColumn::make( 'requestor.name' )->label( 'Created By' )->sortable()->searchable(),
                    TextColumn::make( 'formatted_status' )->label( 'Status' )->sortable( [ 'status' ] )->searchable( [ 'status' ] ),
                    TextColumn::make( 'formatted_priority' )->label( 'Priority' )->sortable( [ 'priority' ] )
                        ->searchable( [ 'priority' ] ),
                    TextColumn::make( 'assignee.name' )->label( 'Assigned To' )->sortable()->searchable(),
                    TextColumn::make( 'timeout_at' )->label( 'Timeout')->sortable()->dateTime(),
                ] )
                ->filters( [
                    SelectFilter::make( 'status' )
                        ->options( Ticket::getFormattedStatusMappings() ),

                    SelectFilter::make( 'priority' )
                        ->options( Ticket::getFormattedPriorityMappings() ),

                ] )
                ->actions( [
                    Action::make( 'assignTicket' )
                        ->label( 'Assign Ticket' )
                        ->visible( fn( Ticket $record ) => $record->assigned_to === null )
                        ->form( function () : array {
                            return [
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
                                    ->required( fn( $get ) => $get( 'action' ) === 'assign_to_agent' )
                            ];
                        } )
                        ->action( function ( array $data, Ticket $record ) {
                            if ( $data['action'] === 'assign_to_self' ) {
                                ( new TicketService() )->assignTicket( $record, auth()->user() );
                            } elseif ( $data['action'] === 'assign_to_agent' ) {
                                $agent = User::findOrFail( $data['agent_id'] );
                                ( new TicketService() )->assignTicket( $record, $agent, 'Admin' );
                            }

                            return redirect()->route( 'filament.app.resources.tickets.view', $record );
                        } )
                        ->modalHeading( 'Assign Ticket' )
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

        private static function getTicketsNavigationItems() : array
        {
            $nav_items = [];
            $mappings = Ticket::getFormattedStatusMappings();
            foreach ( $mappings as  $label => $status ) {
                $nav_items[] = self::createNavigationItem( $label, $status );
            }
            return $nav_items;
        }

        /**
         * Create a navigation item for a specific ticket status.
         *
         * @param  string  $label
         * @param  string  $status
         * @return NavigationItem
         */
        private static function createNavigationItem( string $label, string $status ) : NavigationItem
        {
            return NavigationItem::make( $label )
                ->url( '/app/tickets?tableFilters[status][value]=' . $status )
                ->isActiveWhen( fn(
                ) => request()->fullUrlIs( url( '/app/tickets' ) . '?tableFilters[status][value]=' . $status ) )
                ->icon( 'heroicon-o-ticket' )
                ->group( 'Tickets' )
                ->badge( fn() => Ticket::where( 'status', $status )->count() );
        }
    }