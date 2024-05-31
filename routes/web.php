<?php

    use App\Http\Controllers\ProfileController;
    use App\Http\Controllers\RedirectController;
    use App\Livewire\CreateTicket;
    use App\Livewire\MyTickets;
    use Illuminate\Support\Facades\Route;


    Route::middleware( [ 'auth' ] )->get( '/',
        [ RedirectController::class, 'redirect' ] )->name( 'home' );

//    Route::get( 'app/my-tickets', MyTickets::class )->name( 'tickets.my-tickets' );
//    Route::get( 'app/tickets/create', CreateTicket::class )->name( 'tickets.create' );

    Route::get( '/dashboard', function () {
        return view( 'dashboard' );
    } )->middleware( [ 'auth', 'verified' ] )->name( 'dashboard' );

    Route::middleware( 'auth' )->group( function () {
        Route::get( '/profile', [ ProfileController::class, 'edit' ] )->name( 'profile.edit' );
        Route::patch( '/profile', [ ProfileController::class, 'update' ] )->name( 'profile.update' );
        Route::delete( '/profile', [ ProfileController::class, 'destroy' ] )->name( 'profile.destroy' );
    } );

    require __DIR__ . '/auth.php';