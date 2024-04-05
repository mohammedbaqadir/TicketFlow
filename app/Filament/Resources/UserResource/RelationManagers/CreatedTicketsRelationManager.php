<?php

    namespace App\Filament\Resources\UserResource\RelationManagers;

    use Filament\Forms;
    use Filament\Forms\Components\Textarea;
    use Filament\Forms\Components\TextInput;
    use Filament\Forms\Form;
    use Filament\Resources\RelationManagers\RelationManager;
    use Filament\Tables;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Table;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\SoftDeletingScope;

    class CreatedTicketsRelationManager extends RelationManager
    {
        protected static string $relationship = 'createdTickets';

        public function form(Form $form): Form
        {
            return $form
                ->schema( [
                    TextInput::make( 'title' )
                        ->required()
                        ->maxLength( 255 ),
                    Textarea::make( 'description' )
                        ->required(),
                ] );
        }

        public function table(Table $table): Table
        {
            return $table
                ->recordTitleAttribute('title')
                ->columns([
                    TextColumn::make( 'id' )->sortable(),
                    TextColumn::make( 'title' )->sortable()->searchable(),
                    TextColumn::make( 'description' )->sortable()->searchable(),
                    TextColumn::make( 'status' )->sortable()->searchable(),
                    TextColumn::make( 'priority' )->sortable()->searchable(),
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