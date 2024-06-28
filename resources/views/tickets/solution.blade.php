@extends('components.layout.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-gradient-to-r from-gray-100 to-purple-100 dark:from-gray-600 dark:to-gray-400 shadow-xl rounded-lg overflow-hidden">
            <div class="p-6">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Submit Solution</h1>
                <form action="{{ route('tickets.solutions.store', $ticket) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="content"
                               class="block text-sm font-medium text-gray-700 dark:text-gray-300">Solution</label>
                        <textarea name="content"
                                  id="content"
                                  rows="6"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                  required></textarea>
                    </div>
                    <div class="flex justify-end">
                        <x-button type="submit" variant="primary" class="px-6 py-3">
                            Submit Solution
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection