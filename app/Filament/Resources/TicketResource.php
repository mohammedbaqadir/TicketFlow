<?php

    namespace App\Filament\Resources;

    use App\Filament\Resources\TicketResource\Pages;
    use App\Filament\Resources\TicketResource\RelationManagers;
    use App\Filament\Resources\TicketResource\RelationManagers\AssigneeRelationManager;
    use App\Filament\Resources\TicketResource\RelationManagers\RequestorRelationManager;
    use App\Models\Ticket;
    use Exception;
    use Filament\Forms;
    use Filament\Forms\Components\DateTimePicker;
    use Filament\Forms\Components\Select;
    use Filament\Forms\Components\Textarea;
    use Filament\Forms\Components\TextInput;
    use Filament\Forms\Form;
    use Filament\Resources\Resource;
    use Filament\Tables;
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

        /*       public static function form(Form $form): Form
               {
                   return $form
                       ->schema([
                           TextInput::make( 'title' )
                               ->required()
                               ->maxLength( 255 ),
                           Textarea::make( 'description' )
                               ->required(),
                           Forms\Components\Hidden::make( 'status' )
                               ->default( 'open' ),
                           Forms\Components\Select::make( 'priority' )
                               ->options( [
                                   'low' => 'Low',
                                   'medium' => 'Medium',
                                   'high' => 'High',
                               ] )
                               ->required()
                               ->reactive()
                               ->afterStateUpdated( function ( $state, $set ) {
                                   $set( 'timeout_at', now()->addHours( self::determineTimeout( $state ) ) );
                               } ),
                           Forms\Components\Hidden::make( 'timeout_at' ),
                           Forms\Components\Hidden::make( 'created_by' )
                               ->default( auth()->id() ),
                           Forms\Components\Select::make( 'assigned_to' )
                               ->relationship( 'assignee', 'name' )
                               ->nullable(),

                       ] );
               }*/

        public static function table(Table $table): Table
        {
            return $table
                ->columns([
                    TextColumn::make( 'id' )->sortable(),
                    TextColumn::make( 'title' )->sortable()->searchable(),
                    TextColumn::make( 'description' )->sortable()->searchable(),
                    TextColumn::make( 'status' )->sortable()->searchable(),
                    TextColumn::make( 'priority' )->sortable()->searchable(),
                    TextColumn::make( 'creator.name' )->label( 'Created By' )->sortable()->searchable(),
                    TextColumn::make( 'assignee.name' )->label( 'Assigned To' )->sortable()->searchable(),
                    TextColumn::make( 'created_at' )->sortable()->dateTime(),
                    TextColumn::make( 'timeout_at' )->sortable()->dateTime(),
                ])
                ->filters([
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
                ])
                ->actions([
                    Tables\Actions\EditAction::make(),
                ])
                ->bulkActions([
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ]),
                ]);
        }

        public static function getRelations(): array
        {
            return [
                RequestorRelationManager::class,
                AssigneeRelationManager::class
            ];
        }

        public static function getPages(): array
        {
            return [
                'index' => Pages\ListTickets::route('/'),
                'create' => Pages\CreateTicket::route('/create'),
                'edit' => Pages\EditTicket::route( '/{record}/edit' ),
            ];
        }



    }