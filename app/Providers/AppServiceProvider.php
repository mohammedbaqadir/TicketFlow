<?php

    namespace App\Providers;

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
            //
        }

        /**
         * Bootstrap any application services.
         */
        public function boot(): void
        {
            FilamentColor::register( [
                'danger' => Color::Red,
                'gray' => Color::hex( '#192a35'),
                'info' => Color::hex( '#b3f2f8'),
                'primary' => Color::hex( '#9eace5'),
                'success' => Color::Green,
                'warning' => Color::Amber,
            ] );
        }
    }