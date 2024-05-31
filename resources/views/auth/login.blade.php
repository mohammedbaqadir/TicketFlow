<!-- resources/views/auth/login.blade.php -->
<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-blue-500 to-purple-600 p-6">
        <div class="bg-gray-800 rounded-lg shadow-lg p-8 max-w-md w-full space-y-6">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-gray-400" />
                    <x-text-input id="email"
                                  class="block mt-1 w-full bg-gray-900 text-gray-300 border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                  type="email"
                                  name="email"
                                  :value="old('email')"
                                  required
                                  autofocus
                                  autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" class="text-gray-400" />
                    <x-text-input id="password"
                                  class="block mt-1 w-full bg-gray-900 text-gray-300 border-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                                  type="password"
                                  name="password"
                                  required
                                  autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400" />
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center text-gray-400">
                        <input id="remember_me"
                               type="checkbox"
                               class="rounded bg-gray-900 border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"
                               name="remember">
                        <span class="ml-2 text-sm">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-400 hover:text-gray-300"
                           href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <x-primary-button class="ml-3 bg-indigo-600 hover:bg-indigo-500 focus:ring-2 focus:ring-indigo-500">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>