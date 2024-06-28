<?php
    declare( strict_types = 1 );

    use App\Http\Controllers\RedirectController;
    use App\Http\Controllers\SearchController;
    use App\Http\Controllers\TicketController;
    use Illuminate\Support\Facades\Route;

    Route::middleware( [ 'auth' ] )->group( function () {
        Route::get( '/', [ RedirectController::class, 'redirect' ] )->name( 'home' );

        Route::get( '/my-tickets', [ TicketController::class, 'myTickets' ] )->name( 'my-tickets' );
        Route::resource( 'tickets', TicketController::class );

        Route::get( '/tickets/{ticket}/solutions/create',
            [ SolutionController::class, 'create' ] )->name( 'tickets.solutions.create' );
        Route::post( '/tickets/{ticket}/solutions',
            [ SolutionController::class, 'store' ] )->name( 'tickets.solutions.store' );
        Route::post( '/solutions/{solution}/rate', [ SolutionController::class, 'rate' ] )->name( 'solutions.rate' );

        Route::get( '/search', [ SearchController::class, 'search' ] )->name( 'search' );
    } );

    Route::get( '/error', function () {
        return view( 'error' );
    } )->name( 'error' );


    require __DIR__ . '/auth.php';