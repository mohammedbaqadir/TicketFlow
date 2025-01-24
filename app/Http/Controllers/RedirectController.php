<?php
    declare( strict_types = 1 );

    namespace App\Http\Controllers;


    use Illuminate\Http\RedirectResponse;
    use Illuminate\Routing\Route;

    class RedirectController extends Controller
    {
        public function redirect() : RedirectResponse
        {
            $route = match ( auth()->user()?->role ) {
                'admin' => 'filament.app.pages.dashboard',
                'agent' => 'tickets.index',
                'employee' => 'my-tickets',
                default => 'login',
            };

            return redirect()->route( $route );
        }


    }