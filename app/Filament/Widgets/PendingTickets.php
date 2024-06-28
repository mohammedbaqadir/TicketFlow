<?php
    declare( strict_types = 1 );

    namespace App\Filament\Widgets;

    use App\Models\Ticket;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Filters\SelectFilter;
    use Filament\Tables\Table;
    use Filament\Widgets\TableWidget as BaseWidget;
    use Illuminate\Database\Eloquent\Builder;

    class PendingTickets extends BaseWidget
    {

        public function table( Table $table ) : Table
        {
            return $table
                ->description( 'Listing of tickets that are currently unassigned or pending action.' )
                ->recordUrl( fn( Ticket $record ) : string => route( 'filament.app.resources.tickets.view',
                    [ 'record' => $record ] ) )
                ->query(
                    fn( Builder $query ) => Ticket::query()->whereIn( 'status', ['open', 'awaiting-acceptance', 'elevated' ] )
                )
                ->columns( [
                    TextColumn::make( 'id' ),
                    TextColumn::make( 'title' )->label( 'Title'),
                    TextColumn::make( 'requestor.name' )->label( 'Created By' ),
                    TextColumn::make( 'formatted_status' )->label( 'Status'),
                    TextColumn::make( 'created_at' )->label( 'Created At' )->sortable()->dateTime(),
                ] )->filters( [
                    SelectFilter::make( 'status' )->options( [
                        'open' => 'OPEN',
                        'awaiting-acceptance' => 'AWAITING ACCEPTANCE',
                        'elevated' => 'ELEVATED',
                    ] ),
                ] )
                ->paginated( false );
        }

    }