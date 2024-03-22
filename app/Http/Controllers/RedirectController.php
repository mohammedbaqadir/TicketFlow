<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;

    class RedirectController extends Controller
    {
        public function redirect()
        {
            $route = match ( auth()->user()->role ) {
                'admin' => 'dashboard',
                'agent' => 'tickets',
                'employee' => 'my-tickets',
            };

            return redirect()->route( $route );
        }

    }