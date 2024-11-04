<?php
    declare( strict_types = 1 );

    use App\Http\Controllers\OpcacheController;
    use Illuminate\Support\Facades\Route;

    Route::post( '/api/opcache/reset', [ OpcacheController::class, 'reset' ] )
        ->middleware( [ 'auth:sanctum', 'throttle:opcache-reset' ] )
        ->name( 'opcache.reset' );