<?php
    declare( strict_types = 1 );

    namespace App\Filament\Pages;

    use Filament\Pages\Page;

    class Dashboard extends Page
    {
        protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
        protected ?string $heading = '';
        protected static string $view = 'filament.pages.dashboard';

    }