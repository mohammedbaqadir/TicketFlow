<?php
    declare( strict_types = 1 );

    use App\Http\Controllers\AnswerController;
    use App\Http\Controllers\ProfileController;
    use App\Http\Controllers\RedirectController;
    use App\Http\Controllers\SearchController;
    use App\Http\Controllers\TicketController;
    use App\Http\Controllers\ToastController;
    use App\Http\Controllers\UserPreferenceController;
    use Illuminate\Support\Facades\Route;

    Route::middleware( [ 'auth' ] )->group( function () {
        // Home redirect
        Route::get( '/', [ RedirectController::class, 'redirect' ] )->name( 'home' );

        Route::get( '/profile', [ ProfileController::class, 'index' ] )->name( 'profile.index' );

        Route::get( '/preferences', [ UserPreferenceController::class, 'index' ] )->name( 'preferences.index' );
        Route::post( '/preferences/theme',
            [ UserPreferenceController::class, 'updateTheme' ] )->name( 'preferences.updateTheme' );

        // Search functionality
        Route::get( '/search', [ SearchController::class, 'search' ] )->name( 'search' );

        // Ticket-related routes
        Route::resource( 'tickets', TicketController::class );
        Route::get( '/my-tickets', [ TicketController::class, 'myTickets' ] )->name( 'my-tickets' );
        Route::post( 'tickets/{ticket}/assign', [ TicketController::class, 'assign' ] )->name( 'tickets.assign' );
        Route::post( 'tickets/{ticket}/unassign', [ TicketController::class, 'unassign' ] )->name( 'tickets.unassign' );
        Route::post( '/tickets/{ticket}/meeting', [ TicketController::class, 'meeting' ] )->name( 'tickets.meeting' );
        Route::post( '/meeting/joined', [ TicketController::class, 'meetingJoined' ] )->name( 'meeting.joined' );


        // Answer-related routes
        Route::resource( 'tickets.answers', AnswerController::class )
            ->except( [ 'index', 'show' ] )
            ->shallow();

        // Accept answer route
        Route::post( 'answers/{answer}/accept', [ AnswerController::class, 'accept' ] )->name( 'answers.accept' );
    } );


// Error route
    Route::get( '/error', static function () {
        return view( 'error' );
    } )->name( 'error' );

    Route::post( '/trigger-toast', [ ToastController::class, 'triggerToast' ] )->name( 'trigger-toast' );

    // FAQ Route
    Route::get( '/faq', function () {
        return view( 'faq' );
    } )->name( 'faq' );

// Privacy Policy Route
    Route::get( '/privacy-policy', function () {
        return view( 'privacy-policy' );
    } )->name( 'privacy-policy' );


    require __DIR__ . '/auth.php';