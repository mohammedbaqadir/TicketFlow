<?php

    namespace App\Filament\Resources;

    use App\Filament\Resources\TicketResource\Pages;
    use App\Filament\Resources\TicketResource\Pages\CreateTicket;
    use App\Filament\Resources\TicketResource\Pages\EditTicket;
    use App\Filament\Resources\TicketResource\Pages\ListTickets;
    use App\Filament\Resources\TicketResource\Pages\MyTickets;
    use App\Filament\Resources\TicketResource\Pages\ViewTicket;
    use App\Filament\Resources\TicketResource\RelationManagers;
    use App\Filament\Resources\TicketResource\RelationManagers\AssigneeRelationManager;
    use App\Filament\Resources\TicketResource\RelationManagers\RequestorRelationManager;
    use App\Models\Ticket;
    use App\Models\User;
    use Exception;
    use Filament\Forms;
    use Filament\Forms\Components\DateTimePicker;
    use Filament\Forms\Components\Select;
    use Filament\Forms\Components\Textarea;
    use Filament\Forms\Components\TextInput;
    use Filament\Forms\Form;
    use Filament\Resources\Resource;
    use Filament\Tables;
    use Filament\Tables\Actions\BulkActionGroup;
    use Filament\Tables\Actions\DeleteBulkAction;
    use Filament\Tables\Actions\EditAction;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Filters\SelectFilter;
    use Filament\Tables\Table;
    use GeminiAPI\Laravel\Facades\Gemini;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\SoftDeletingScope;
    use Illuminate\Support\Facades\Log;

    class TicketResource extends Resource
    {
        protected static ?string $model = Ticket::class;

        protected static ?string $navigationIcon = 'heroicon-o-ticket';

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
                ->recordUrl( fn( Ticket $record ) => route( 'tickets.show', $record ) )
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
                    EditAction::make(),
                ] )
                ->bulkActions( [
                    BulkActionGroup::make( [
                        DeleteBulkAction::make(),
                    ] ),
                ] );
        }

        public static function getRelations() : array
        {
            return [
                RequestorRelationManager::class,
                AssigneeRelationManager::class
            ];
        }

        public static function getPages() : array
        {
            return [
                'index' => ListTickets::route( '/' ),
                'create' => CreateTicket::route( '/create' ),
                'edit' => EditTicket::route( '/{record}/edit' ),
                'view' => ViewTicket::route( '/{record}'),
                'myTickets' => MyTickets::route( 'my-tickets' ),
            ];
        }


    }