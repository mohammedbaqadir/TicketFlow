<?php

    namespace App\Filament\Widgets;

    use App\Models\Ticket;
    use Filament\Tables;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Filters\SelectFilter;
    use Filament\Tables\Table;
    use Filament\Widgets\TableWidget as BaseWidget;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;

    class PendingTickets extends BaseWidget
    {
        public function table( Table $table ) : Table
        {
            return $table
                ->description( 'Listing of tickets that are currently unassigned or pending action.' )
                ->query(
                    fn( Builder $query ) => Ticket::query()->where( 'status', 'open' )->orWhere( 'status',
                        'awaiting-acceptance' )->orWhere( 'status',
                        'elevated' )
                )
                ->columns( [
                    TextColumn::make( 'id' ),
                    TextColumn::make( 'title' ),
                    TextColumn::make( 'requestor.name' )->label( 'Requestor' ),
                    TextColumn::make( 'status' )->label( 'Status'),
                    TextColumn::make( 'created_at' )->label( 'Created' )->sortable()->dateTime(),
                ] )->filters( [
                    SelectFilter::make( 'status' )->options( [
                        'open' => 'Open',
                        'awaiting-acceptance' => 'Awaiting Acceptance',
                        'elevated' => 'Elevated',
                    ] ),
                ] )
                ->paginated( false );
        }

    }