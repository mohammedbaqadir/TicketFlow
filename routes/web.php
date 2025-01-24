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
    use Illuminate\View\View;

// Public routes
    Route::get( '/error', static function () : View {
        return view( 'error' );
    } )->name( 'error' );

    Route::get( '/faq', function () : View {
        return view( 'faq' );
    } )->name( 'faq' );

    Route::get( '/privacy-policy', function () : View {
        return view( 'privacy-policy' );
    } )->name( 'privacy-policy' );

// Authentication routes (already defined in auth.php)
    require __DIR__ . '/auth.php';

// Authenticated routes with global rate limiting
    Route::middleware( [ 'auth', 'throttle:global' ] )->group( function () {
        Route::get( '/', [ RedirectController::class, 'redirect' ] )->name( 'home' );
        Route::get( '/profile', [ ProfileController::class, 'index' ] )->name( 'profile.index' );

        Route::get( '/preferences', [ UserPreferenceController::class, 'index' ] )->name( 'preferences.index' );
        Route::post( '/preferences/theme',
            [ UserPreferenceController::class, 'updateTheme' ] )->name( 'preferences.updateTheme' );

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
        Route::post( 'answers/{answer}/accept', [ AnswerController::class, 'accept' ] )->name( 'answers.accept' );
    } );

// Routes with specific rate limiting
    Route::middleware( [ 'auth', 'throttle:auth' ] )->group( function () {
        // Add any routes that need more stringent rate limiting here
    } );

// Toast route (consider if this needs authentication)
    Route::post( '/trigger-toast', [ ToastController::class, 'triggerToast' ] )->name( 'trigger-toast' );