<?php
    declare( strict_types = 1 );

    use App\Http\Controllers\AnswerController;
    use App\Http\Controllers\CommentController;
    use App\Http\Controllers\RedirectController;
    use App\Http\Controllers\SearchController;
    use App\Http\Controllers\TicketController;
    use Illuminate\Support\Facades\Route;

    Route::middleware( [ 'auth' ] )->group( function () {
        Route::get( '/', [ RedirectController::class, 'redirect' ] )->name( 'home' );
        Route::get( '/search', [ SearchController::class, 'search' ] )->name( 'search' );

        Route::resource( 'tickets', TicketController::class );
        Route::get( '/my-tickets', [ TicketController::class, 'myTickets' ] )->name( 'my-tickets' );
        Route::post( 'tickets/{ticket}/assign', [ TicketController::class, 'assign' ] )->name( 'tickets.assign' );
        Route::post( 'tickets/{ticket}/unassign', [ TicketController::class, 'unassign' ] )->name( 'tickets.unassign' );

        Route::resource( 'tickets.answers', AnswerController::class )->shallow();
        Route::post( 'answers/{answer}/accept', [ AnswerController::class, 'accept' ] )->name( 'answers.accept' );

        Route::resource( 'answers.comments', CommentController::class )
            ->shallow()
            ->only( [ 'store', 'update', 'destroy' ] )
            ->names( [
                'store' => 'answers.comments.store',
                'update' => 'answers.comments.update',
                'destroy' => 'answers.comments.destroy'
            ] );

        Route::resource( 'tickets.comments', CommentController::class )
            ->shallow()
            ->only( [ 'store', 'update', 'destroy' ] )
            ->names( [
                'store' => 'tickets.comments.store',
                'update' => 'tickets.comments.update',
                'destroy' => 'tickets.comments.destroy'
            ] );
    } );


    Route::get( '/error', function () {
        return view( 'error' );
    } )->name( 'error' );


    require __DIR__ . '/auth.php';