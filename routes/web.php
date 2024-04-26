<?php

    use App\Filament\Pages\Dashboard;
    use App\Filament\Resources\TicketResource;
    use App\Filament\Resources\UserResource;
    use App\Http\Controllers\ProfileController;
    use App\Http\Controllers\RedirectController;
    use App\Livewire\AssignTicketModal;
    use Illuminate\Support\Facades\Route;

    Route::middleware( [ 'auth' ] )->get( '/',
        [ RedirectController::class, 'redirect' ] )->name( 'home' );


    Route::get( '/tickets/{record}/assign',
        AssignTicketModal::class )->name( 'filament.app.resources.tickets.showAssignModal' );


    Route::get( '/dashboard', function () {
        return view( 'dashboard' );
    } )->middleware( [ 'auth', 'verified' ] )->name( 'dashboard' );

    Route::middleware( 'auth' )->group( function () {
        Route::get( '/profile', [ ProfileController::class, 'edit' ] )->name( 'profile.edit' );
        Route::patch( '/profile', [ ProfileController::class, 'update' ] )->name( 'profile.update' );
        Route::delete( '/profile', [ ProfileController::class, 'destroy' ] )->name( 'profile.destroy' );
    } );

    require __DIR__ . '/auth.php';