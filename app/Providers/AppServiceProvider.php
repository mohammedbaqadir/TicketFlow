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
        }

        /**
         * Bootstrap any application services.
         */
        public function boot(): void
        {

        }
    }