<?php
    declare( strict_types = 1 );

    namespace App\Providers\Filament;

    use App\Filament\Pages\Dashboard;
    use App\Http\Middleware\FilamentAdminMiddleware;
    use Filament\Http\Middleware\Authenticate;
    use Filament\Http\Middleware\DisableBladeIconComponents;
    use Filament\Http\Middleware\DispatchServingFilamentEvent;
    use Filament\Panel;
    use Filament\PanelProvider;
    use Filament\Support\Colors\Color;
    use Filament\Widgets;
    use FilipFonal\FilamentLogManager\FilamentLogManager;
    use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
    use Illuminate\Cookie\Middleware\EncryptCookies;
    use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
    use Illuminate\Routing\Middleware\SubstituteBindings;
    use Illuminate\Session\Middleware\AuthenticateSession;
    use Illuminate\Session\Middleware\StartSession;
    use Illuminate\View\Middleware\ShareErrorsFromSession;

    class AppPanelProvider extends PanelProvider
    {
        public function panel( Panel $panel ) : Panel
        {
            return $panel
                ->default()
                ->id( 'app' )
                ->path( 'app' )
                ->topNavigation()->colors( [
                    'danger' => Color::Red,
                    'gray' => Color::hex( '#192a35' ),
                    'info' => Color::hex( '#b3f2f8' ),
                    'primary' => Color::hex( '#9eace5' ),
                    'success' => Color::Green,
                    'warning' => Color::Amber,
                ])
                ->viteTheme( 'resources/css/filament/app/theme.css')
                ->brandName( 'TicketFlow')
                ->brandLogo( asset( 'images/logo.png' ))
                ->favicon( asset( 'favicon.ico' ) )
                ->discoverResources( in: app_path( 'Filament/Resources' ), for: 'App\\Filament\\Resources' )
                ->discoverPages( in: app_path( 'Filament/Pages' ), for: 'App\\Filament\\Pages' )
                ->pages( [
                    Dashboard::class,
                ] )
                ->discoverWidgets( in: app_path( 'Filament/Widgets' ), for: 'App\\Filament\\Widgets' )
                ->widgets( [
                    Widgets\AccountWidget::class,
                    Widgets\FilamentInfoWidget::class,
                ] )
                ->middleware( [
                    EncryptCookies::class,
                    AddQueuedCookiesToResponse::class,
                    StartSession::class,
                    AuthenticateSession::class,
                    ShareErrorsFromSession::class,
                    VerifyCsrfToken::class,
                    SubstituteBindings::class,
                    DisableBladeIconComponents::class,
                    DispatchServingFilamentEvent::class,
                ] )
                ->authMiddleware( [
                    Authenticate::class,
                    FilamentAdminMiddleware::class
                ] )->breadcrumbs( false )
                ->darkMode(false)
                ->plugins( [
                    FilamentLogManager::make(),
                ] );
        }
    }