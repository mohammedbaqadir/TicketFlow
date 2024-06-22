<?php

    namespace App\Filament\Widgets;

    use App\Models\Ticket;
    use App\Traits\HasCustomRecordUrl;
    use Filament\Tables;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Filters\SelectFilter;
    use Filament\Tables\Table;
    use Filament\Widgets\TableWidget as BaseWidget;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;

    class PendingTickets extends BaseWidget
    {
        use HasCustomRecordUrl;

        public function table( Table $table ) : Table
        {
            return $table
                ->description( 'Listing of tickets that are currently unassigned or pending action.' )
                ->recordUrl( ( new static() )->getTableRecordUrlUsing())
                ->query(
                    fn( Builder $query ) => Ticket::query()->whereIn( 'status', ['open', 'awaiting-acceptance', 'elevated' ] )
                )
                ->columns( [
                    TextColumn::make( 'id' ),
                    TextColumn::make( 'title' ),
                    TextColumn::make( 'requestor.name' )->label( 'Requestor' ),
                    TextColumn::make( 'formatted_status' )->label( 'Status'),
                    TextColumn::make( 'created_at' )->label( 'Created' )->sortable()->dateTime(),
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