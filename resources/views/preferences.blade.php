@extends('components.layout.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-white to-blue-100
    dark:from-gray-800 dark:to-gray-900 p-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-8 transition duration-300 ease-in-out transform hover:shadow-xl hover:scale-[1.01]">
            <!-- Heading -->
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-200 mb-8 text-center">User Preferences</h1>


            <!-- Theme Selection -->
            <div>
                <label for="theme-select" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Choose
                                                                                                                    Your
                                                                                                                    Theme</label>
                <select name="theme" id="theme-select"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-300 ease-in-out transform hover:scale-105">
                    <option value="light" {{ Auth::user()->preferred_theme === 'light' ? 'selected' : '' }}>Light
                    </option>
                    <option value="dark" {{ Auth::user()->preferred_theme === 'dark' ? 'selected' : '' }}>Dark
                    </option>
                </select>
            </div>


        </div>
    </div>
@endsection