<?php

    namespace App\Filament\Resources\TicketResource\Pages;

    use App\Filament\Resources\TicketResource;
    use App\Models\Ticket;
    use Filament\Actions;
    use Filament\Actions\CreateAction;
    use Filament\Resources\Pages\ListRecords;
    use Filament\Tables\Actions\DeleteBulkAction;
    use Filament\Tables\Actions\EditAction;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Filters\SelectFilter;
    use Filament\Tables\Table;
    use Illuminate\Database\Eloquent\Builder;

    class MyTickets extends ListRecords
    {
        protected static string $resource = TicketResource::class;
        public static function canAccess( array $parameters = [] ) : bool
        {
            return userHasRole( 'employee');
        }

        /**
         * Configure the table.
         *
         * @param  Table  $table
         * @return Table
         */
        public function table( Table $table ) : Table
        {
            return $table
                ->query( fn( Builder $query ) => $query->where( 'created_by', auth()->id() ) )
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
                    DeleteBulkAction::make(),
                ] );
        }

        /**
         * Define the actions for this page.
         *
         * @return array
         */
        protected function getHeaderActions() : array
        {
            return [
                CreateAction::make(),
            ];
        }

    }