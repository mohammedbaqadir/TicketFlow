<?php

    use App\Filament\Pages\Dashboard;
    use App\Http\Controllers\ProfileController;
    use App\Http\Controllers\RedirectController;
    use Illuminate\Support\Facades\Route;

Route::get( 'test', function () {
    return bcrypt( 'Overlord Primary Guy');
});
    Route::middleware( [ 'auth' ] )->get( '/',
        [ RedirectController::class, 'redirect' ] )->name( 'home' );

    Route::middleware( [ 'auth', 'role:admin' ] )->group( function () {
        Route::get( 'dashboard', Dashboard::class )->name( 'dashboard' );
    } );

//    Route::get( '/dashboard', function () {
//        return view( 'dashboard' );
//    } )->middleware( [ 'auth', 'verified' ] )->name( 'dashboard' );

//    Route::middleware( 'auth' )->group( function () {
//        Route::get( '/profile', [ ProfileController::class, 'edit' ] )->name( 'profile.edit' );
//        Route::patch( '/profile', [ ProfileController::class, 'update' ] )->name( 'profile.update' );
//        Route::delete( '/profile', [ ProfileController::class, 'destroy' ] )->name( 'profile.destroy' );
//    } );

    require __DIR__ . '/auth.php';