<?php
    declare( strict_types = 1 );

    namespace App\Filament\Resources;

    use App\Filament\Resources\UserResource\Pages\CreateUser;
    use App\Filament\Resources\UserResource\Pages\EditUser;
    use App\Filament\Resources\UserResource\Pages\ListUsers;
    use App\Filament\Resources\UserResource\Pages\ViewUser;
    use App\Models\User;
    use Filament\Forms\Components\Checkbox;
    use Filament\Forms\Components\Select;
    use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
    use Filament\Forms\Components\TextInput;
    use Filament\Forms\Form;
    use Filament\Resources\Resource;
    use Filament\Tables\Actions\DeleteAction;
    use Filament\Tables\Actions\DeleteBulkAction;
    use Filament\Tables\Actions\EditAction;
    use Filament\Tables\Columns\CheckboxColumn;
    use Filament\Tables\Columns\ImageColumn;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Table;

    class UserResource extends Resource
    {
        protected static ?string $model = User::class;


        public static function form( Form $form ) : Form
        {
            return $form
                ->schema( [
                    TextInput::make( 'name' )
                        ->required()
                        ->maxLength( 255 ),
                    TextInput::make( 'email' )
                        ->email()
                        ->unique( 'users', 'email' )
                        ->required()
                        ->maxLength( 255 ),
                    TextInput::make( 'password' )
                        ->password()
                        ->confirmed()
                        ->required( fn( $livewire ) => $livewire instanceof CreateUser )
                        ->minLength( 8 )
                        ->maxLength( 255 ),
                    TextInput::make( 'password_confirmation' )
                        ->password()
                        ->required( fn( $livewire ) => $livewire instanceof CreateUser )
                        ->minLength( 8 )
                        ->maxLength( 255 )
                        ->label( 'Confirm Password' ),
                    Select::make( 'role' )
                        ->options( [
                            'employee' => 'Employee',
                            'agent' => 'Agent',
                            'admin' => 'Admin',
                        ] )
                        ->required(),
                    Checkbox::make( 'is_locked' )->label( 'Locked?' )->inline(),
                    SpatieMediaLibraryFileUpload::make( 'avatar' )
                        ->collection( 'avatar' )
                        ->label( 'Avatar' )
                ] );
        }

        public static function table( Table $table ) : Table
        {
            return $table
                ->columns( [
                    ImageColumn::make( 'avatar_url' )
                        ->label( 'Avatar' )
                        ->getStateUsing( fn( User $record ) => $record->getFirstMediaUrl( 'avatar' ) )
                        ->size( 50 ),
                    TextColumn::make( 'id' )->sortable(),
                    TextColumn::make( 'name' )->label( 'Name' )->sortable()->searchable(),
                    TextColumn::make( 'email' )->label( 'Email' )->sortable()->searchable(),
                    TextColumn::make( 'role' )->label( 'Role' )->sortable(),
                    CheckboxColumn::make( 'is_locked' )->label( 'Locked?' )->inline(),
                    TextColumn::make( 'created_at' )->label( 'Created At' )->sortable()->dateTime(),
                ] )
                ->actions( [
                    EditAction::make(),
                    DeleteAction::make(),
                ] )
                ->bulkActions( [
                    DeleteBulkAction::make(),
                ] );
        }


        public static function getRelations() : array
        {
            return [

            ];
        }

        public static function getPages() : array
        {
            return [
                'index' => ListUsers::route( '/' ),
                'create' => CreateUser::route( '/create' ),
                'edit' => EditUser::route( '/{record}/edit' ),
                'view' => ViewUser::route( '/{record}' ),
            ];
        }
    }