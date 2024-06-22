<?php

    use App\Http\Controllers\ProfileController;
    use App\Http\Controllers\RedirectController;
    use Illuminate\Support\Facades\Route;


    Route::middleware( [ 'auth' ] )->get( '/',
        [ RedirectController::class, 'redirect' ] )->name( 'home' );


    require __DIR__ . '/auth.php';