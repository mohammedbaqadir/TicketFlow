@extends('components.layout.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-gradient-to-r from-gray-100 to-purple-100 dark:from-gray-600 dark:to-gray-400 shadow-xl rounded-lg overflow-hidden">
            <div class="p-6">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Edit Ticket</h1>
                <form action="{{ route('tickets.update', $ticket) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="title"
                               class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                        <input type="text"
                               name="title"
                               id="title"
                               value="{{ old('title', $ticket->title) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <textarea name="description"
                                  id="description"
                                  rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('description', $ticket->description) }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                        <select name="priority"
                                id="priority"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="low" {{ old('priority', $ticket->priority) === 'low' ? 'selected' : '' }}>
                                Low
                            </option>
                            <option value="medium" {{ old('priority', $ticket->priority) === 'medium' ? 'selected' : '' }}>
                                Medium
                            </option>
                            <option value="high" {{ old('priority', $ticket->priority) === 'high' ? 'selected' : '' }}>
                                High
                            </option>
                        </select>
                    </div>
                    <div class="flex justify-end">
                        <x-button type="submit" variant="primary" class="px-6 py-3">
                            Update Ticket
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection