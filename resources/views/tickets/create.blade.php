@extends('components.layout.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-gradient-to-r from-gray-100 to-purple-100 dark:from-gray-600 dark:to-gray-400 shadow-xl rounded-lg overflow-hidden">
            <div class="p-6">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Create New Ticket</h1>
                <form action="{{ route('tickets.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="title"
                               class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                        <input type="text"
                               name="title"
                               id="title"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               required>
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <textarea name="description"
                                  id="description"
                                  rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                  required></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                        <select name="priority"
                                id="priority"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                required>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div class="flex justify-end">
                        <x-button type="submit" variant="primary" class="px-6 py-3">
                            Create Ticket
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection