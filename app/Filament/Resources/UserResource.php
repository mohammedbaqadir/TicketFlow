<?php

    namespace App\Filament\Resources;

    use App\Filament\Forms\Components\PasswordInput;
    use App\Filament\Resources\UserResource\Pages\CreateUser;
    use App\Filament\Resources\UserResource\Pages\EditUser;
    use App\Filament\Resources\UserResource\Pages\ListUsers;
    use App\Filament\Resources\UserResource\Pages\ShowUser;
    use App\Filament\Resources\UserResource\Pages\ViewUser;
    use App\Helpers\NavigationHelper;
    use App\Models\User;
    use Exception;
    use Filament\Forms\Components\Select;
    use Filament\Forms\Components\TextInput;
    use Filament\Forms\Form;
    use Filament\Navigation\NavigationItem;
    use Filament\Resources\Resource;
    use Filament\Tables\Actions\BulkActionGroup;
    use Filament\Tables\Actions\DeleteAction;
    use Filament\Tables\Actions\DeleteBulkAction;
    use Filament\Tables\Actions\EditAction;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Filters\SelectFilter;
    use Filament\Tables\Table;

    class UserResource extends Resource
    {
        protected static ?string $model = User::class;


        public static function getNavigationItems() : array
        {
            return [
                NavigationItem::make( 'Agents' )
                    ->url( '/app/users?tableFilters[role][value]=agent' )
                    ->icon( 'heroicon-o-user-group' )
                    ->group( 'Users' )
                    ->isActiveWhen( fn() => NavigationHelper::isActiveNavigationItem( 'app/users', ['role' =>
                        'agent' ]) )
                    ->badge( fn() => User::isAgent()->count() ),
                NavigationItem::make( 'Emplyees' )
                    ->url( '/app/users?tableFilters[role][value]=employee' )
                    ->icon( 'heroicon-o-user-group' )
                    ->group( 'Users' )
                    ->isActiveWhen( fn() => NavigationHelper::isActiveNavigationItem( 'app/users', ['role' =>
                        'employee'] ) )
                    ->badge( fn() => User::isEmployee()->count() )
            ];
        }


        public static function form( Form $form ) : Form
        {
            return $form
                ->schema( [
                    TextInput::make( 'name' )
                        ->required()
                        ->maxLength( 255 ),
                    TextInput::make( 'email' )
                        ->email()
                        ->unique('users', 'email')
                        ->required()
                        ->maxLength( 255 ),
                    TextInput::make( 'password' )
                        ->password()
                        ->confirmed()
                        ->required( fn( $livewire
                        ) => $livewire instanceof CreateUser )
                        ->minLength( 8 )
                        ->maxLength( 255 ),
                    TextInput::make( 'password_confirmation' )
                        ->password()
                        ->required( fn( $livewire
                        ) => $livewire instanceof CreateUser )
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
                ] );
        }

        /**
         * @throws Exception
         */
        public static function table( Table $table ) : Table
        {
            return $table
                ->columns( [
                    TextColumn::make( 'id' )->sortable(),
                    TextColumn::make( 'name' )->sortable()->searchable(),
                    TextColumn::make( 'email' )->sortable()->searchable(),
                    TextColumn::make( 'role' )->sortable(),
                    TextColumn::make( 'created_at' )->sortable()->dateTime(),
                ] )
                ->recordUrl( fn( User $record ) => route( 'filament.app.resources.users.view', $record ) )
                ->filters( [
                    SelectFilter::make( 'role' )
                        ->options( [
                            'employee' => 'Employee',
                            'agent' => 'Agent',
                            'admin' => 'Admin',
                        ] ),
                ] )
                ->actions( [
                    EditAction::make(),
                    DeleteAction::make()
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