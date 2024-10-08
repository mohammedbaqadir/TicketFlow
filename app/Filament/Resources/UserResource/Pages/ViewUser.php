<?php
    declare( strict_types = 1 );

    namespace App\Filament\Resources\UserResource\Pages;

    use App\Filament\Resources\UserResource;
    use App\Filament\Resources\UserResource\RelationManagers\AssignedTicketsRelationManager;
    use App\Filament\Resources\UserResource\RelationManagers\CreatedTicketsRelationManager;
    use Filament\Actions;
    use Filament\Resources\Pages\ViewRecord;

    class ViewUser extends ViewRecord
    {
        protected static string $resource = UserResource::class;


    }