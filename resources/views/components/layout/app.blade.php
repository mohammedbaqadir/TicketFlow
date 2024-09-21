<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script>
          const theme = '{{ Auth::check() ? Auth::user()->preferred_theme : 'light' }}';
          if (theme === 'dark') {
            document.documentElement.classList.add('dark');
          }
        </script>

        <link rel="stylesheet" href="{{ asset('css/flash.css') }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Preload the Exo 2 font stylesheet -->
        <link rel="preload"
              href="https://fonts.googleapis.com/css2?family=Exo+2:ital,wght@0,400;1,900&display=swap"
              as="style"
              onload="this.rel='stylesheet'">
        <noscript>
            <link href="https://fonts.googleapis.com/css2?family=Exo+2:ital,wght@0,400;1,900&display=swap"
                  rel="stylesheet">
        </noscript>

        <!-- Favicons -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">

        <!-- Styles and Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body data-theme="{{ Auth::check() ? Auth::user()->preferred_theme : 'light' }}" class="font-sans antialiased bg-gray-50
    dark:bg-gray-900 flex flex-col min-h-screen">
        <!-- Main wrapper to ensure footer sticks to bottom -->
        <div class="flex-grow">
            <!-- Navbar -->
            <x-layout.navbar />

            <!-- Main content area -->
            <main class="p-4 pt-20 shadow-inner bg-gray-50 dark:bg-gray-900 transition-all duration-300 ease-in-out min-h-[calc(100vh-6rem)]">
                <div class="container mx-auto">
                    <!-- Error Handling -->
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

        <!-- Footer Implementation -->
        <footer class="bg-gradient-to-r from-gray-200 to-gray-300 dark:from-gray-800 dark:to-gray-900 text-gray-800 dark:text-gray-300 py-6 mt-auto">
            <div class="container mx-auto flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0 px-4 md:px-0">
                <!-- Left side: Company & Copyright Information -->
                <div class="flex flex-col items-center md:items-start space-y-1">
                    <p class="font-bold">&copy; {{ now()->year }} SkyFleet. All rights reserved.</p>
                    <p class="text-xs">TicketFlow - Version {{ config('app.version', '1.0.0') }}</p>
                </div>

                <!-- Center: Useful Links -->
                <div class="flex space-x-4">
                    <a href="{{ route('faq') }}"
                       class="text-gray-600 dark:text-gray-400 hover:text-blue-500 transition duration-300">FAQ</a>
                    <span>|</span>
                    <a href="{{ route('privacy-policy') }}"
                       class="text-gray-600 dark:text-gray-400 hover:text-blue-500 transition duration-300">Privacy
                                                                                                            Policy</a>
                </div>

                <!-- Right side: Contact Information -->
                <div class="text-center md:text-right">
                    <p>Contact Us:</p>
                    <a href="mailto:support@skyfleet.com" class="text-blue-600 dark:text-blue-400 hover:underline">support@skyfleet.com</a>
                </div>
            </div>
        </footer>

        <!-- Scripts -->
        @stack('scripts')
        <x-toast />
        <x-toast-trigger />
    </body>
</html>