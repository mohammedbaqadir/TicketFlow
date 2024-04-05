<?php

    namespace App\Filament\Resources\TicketResource\RelationManagers;

    use Filament\Forms;
    use Filament\Forms\Components\TextInput;
    use Filament\Forms\Form;
    use Filament\Resources\RelationManagers\RelationManager;
    use Filament\Tables;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Table;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\SoftDeletingScope;

    class AssigneeRelationManager extends RelationManager
    {
        protected static string $relationship = 'assignee';
        protected static ?string $recordTitleAttribute = 'name';


        public function form(Form $form): Form
        {
            return $form
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                ]);
        }

        public function table(Table $table): Table
        {
            return $table
                ->recordTitleAttribute('name')
                ->columns([
                    TextColumn::make( 'id' )->sortable(),
                    TextColumn::make( 'name' )->sortable()->searchable(),
                    TextColumn::make( 'email' )->sortable()->searchable(),
                    TextColumn::make( 'created_at' )->sortable()->dateTime(),
                ])
                ->filters([
                    //
                ])
                ->headerActions([
                    Tables\Actions\CreateAction::make(),
                ])
                ->actions([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->bulkActions([
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ]),
                ]);
        }
    }