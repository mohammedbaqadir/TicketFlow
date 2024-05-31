<?php

    namespace App\Http\Controllers;


    use Illuminate\Support\Facades\Route;

    class RedirectController extends Controller
    {
        public function redirect()
        {
            $route = match ( auth()->user()->role ) {
                'admin' => 'filament.app.pages.dashboard',
                'agent' => 'filament.app.resources.tickets.index',
                'employee' => 'filament.app.pages.my-tickets',
//                'employee' => 'tickets.my-tickets',
            };
            return redirect()->route( $route );
        }

    }