<?php
    declare( strict_types = 1 );

    namespace App\Providers;

    use App\Services\AnswerService;
    use App\Services\CommentService;
    use App\Services\TicketService;
    use Filament\Support\Colors\Color;
    use Filament\Support\Facades\FilamentColor;
    use Illuminate\Support\ServiceProvider;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * Register any application services.
         */
        public function register(): void
        {
            $this->app->singleton( TicketService::class, function ( $app ) {
                return new TicketService();
            } );

            $this->app->singleton( AnswerService::class, function ( $app ) {
                return new AnswerService();
            } );

            $this->app->singleton( CommentService::class, function ( $app ) {
                return new CommentService();
            } );
        }

        /**
         * Bootstrap any application services.
         */
        public function boot(): void
        {

        }
    }