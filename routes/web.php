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
        Route::get( '/newshow/{ticket}', [TicketController::class, 'newshow']);
        Route::resource( 'tickets', TicketController::class );
        Route::get( '/my-tickets', [ TicketController::class, 'myTickets' ] )->name( 'my-tickets' );
        Route::post( 'tickets/{ticket}/assign', [ TicketController::class, 'assign' ] )->name( 'tickets.assign' );
        Route::post( 'tickets/{ticket}/unassign', [ TicketController::class, 'unassign' ] )->name( 'tickets.unassign' );

        Route::resource( 'tickets.answers', AnswerController::class )
            ->except( [ 'index', 'show' ] )
            ->shallow();
        /*
            tickets.answers.create  (GET /tickets/{ticket}/answers/create)
            tickets.answers.store   (POST /tickets/{ticket}/answers)
            answers.edit    (GET /answers/{answer}/edit)
            answers.update  (PUT/PATCH /answers/{answer})
            answers.destroy     (DELETE /answers/{answer})
*/

        Route::post( 'answers/{answer}/accept', [ AnswerController::class, 'accept' ] )->name( 'answers.accept' );

        Route::post( 'tickets/{ticket}/comments', [ CommentController::class, 'storeOnTicket' ] )
            ->name( 'tickets.comments.store' );

        Route::post( 'answers/{answer}/comments', [ CommentController::class, 'storeOnAnswer' ] )
            ->name( 'answers.comments.store' );

        Route::delete( 'comments/{comment}', [ CommentController::class, 'destroy' ] )
            ->name( 'comments.destroy' );
    } );


    Route::get( '/error', function () {
        return view( 'error' );
    } )->name( 'error' );


    require __DIR__ . '/auth.php';