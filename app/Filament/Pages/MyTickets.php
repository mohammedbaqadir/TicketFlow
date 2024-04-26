<?php

    namespace App\Filament\Pages;

    use App\Models\Ticket;
    use App\Helpers\AuthHelper;
    use App\Helpers\TicketHelper;
    use Filament\Pages\Page;
    use Filament\Tables;
    use Filament\Tables\Contracts\HasTable;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Filters\SelectFilter;
    use Filament\Actions\CreateAction;
    use Illuminate\Database\Eloquent\Builder;
    use Filament\Forms\Components\TextInput;
    use Filament\Forms\Components\Textarea;

    class MyTickets extends Page implements HasTable
    {
        use Tables\Concerns\InteractsWithTable;

        protected static ?string $navigationIcon = 'heroicon-o-document-text';

        protected static string $view = 'filament.pages.my-tickets';

        public static function canAccess( array $parameters = [] ) : bool
        {
            return AuthHelper::userHasRole( 'employee' );
        }

        protected function getTableQuery() : Builder
        {
            return Ticket::query()->where( 'created_by', auth()->id() );
        }

        protected function getTableColumns() : array
        {
            return [
                TextColumn::make( 'id' )->sortable(),
                TextColumn::make( 'title' )->sortable()->searchable(),
                TextColumn::make( 'description' )->sortable()->searchable(),
                TextColumn::make( 'status' )->sortable()->searchable(),
                TextColumn::make( 'priority' )->sortable()->searchable(),
                TextColumn::make( 'requestor.name' )->label( 'Created By' )->sortable()->searchable(),
                TextColumn::make( 'assignee.name' )->label( 'Assigned To' )->sortable()->searchable(),
                TextColumn::make( 'created_at' )->sortable()->dateTime(),
                TextColumn::make( 'timeout_at' )->sortable()->dateTime(),
            ];
        }

        protected function getTableFilters() : array
        {
            return [
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
            ];
        }

        protected function getFormSchema() : array
        {
            return [
                TextInput::make( 'title' )
                    ->required()
                    ->maxLength( 255 ),
                Textarea::make( 'description' )
                    ->required(),
            ];
        }

        protected function mutateFormDataBeforeCreate( array $data ) : array
        {
            $data['status'] = 'open';
            $data['created_by'] = auth()->id();
            $data['priority'] = TicketHelper::determinePriority( $data['title'], $data['description'] );
            $data['timeout_at'] = now()->addHours( TicketHelper::determineTimeout( $data['priority'] ) );
            $data['assigned_to'] = null;

            return $data;
        }

        protected function getHeaderActions() : array
        {
            return [
                CreateAction::make()
                    ->model( Ticket::class )
                    ->mutateFormDataUsing( fn( array $data ) => $this->mutateFormDataBeforeCreate( $data ) )
                    ->form( $this->getFormSchema() )
                    ->label( 'New Ticket' ),
            ];
        }
    }