<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" >
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="apple-touch-icon" sizes="180x180" href="{{asset('apple-touch-icon.png')}}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{asset('favicon-32x32.png')}}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{asset('favicon-16x16.png')}}">
        <link rel="manifest" href="{{asset('site.webmanifest')}}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
        <div class="antialiased bg-gray-50 dark:bg-gray-900">
            <x-layout.navbar />
            <main class="p-4 h-auto pt-20 shadow-inner bg-gray-50 dark:bg-gray-900 transition-all duration-300 ease-in-out">
                <div class="container mx-auto">
                    @if ($errors->any())
                        <div class="bg-red-500 text-white p-4 rounded mb-4">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                    @yield('content')
                </div>
            </main>
        </div>
    </body>
</html>