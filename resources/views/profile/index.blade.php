@extends('components.layout.app')

@section('content')
    <div class="container mx-auto p-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Profile Information</h1>

            <div class="mb-6 flex items-center space-x-4">
                <img class="w-24 h-24 rounded-full mb-4"
                     src="{{ $user->getFirstMediaUrl('avatar') ?: asset('/images/default-avatar.jpg') }}"
                     alt="user photo">
                <div>
                    <p class="text-lg text-gray-900 dark:text-white"><strong>Name:</strong> {{ $user->name }}</p>
                    <p class="text-lg text-gray-900 dark:text-white"><strong>Email:</strong> {{ $user->email }}</p>
                    <p class="text-lg text-gray-900 dark:text-white"><strong>Role:</strong> {{ ucfirst($role) }}</p>
                    <p class="text-lg text-gray-900 dark:text-white"><strong>Member
                                                                             Since:</strong> {{ $user->created_at->format('F j, Y') }}
                    </p>
                </div>
            </div>

            @if ($role === 'employee')
                <div class="mt-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Employee Ticket Statistics</h2>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach ($stats['tickets'] as $status => $count)
                            <div class="p-4 bg-blue-50 dark:bg-blue-900 rounded-lg shadow">
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $status)) }}
                                    Tickets</p>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-300">{{ $count }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($role === 'agent')
                <div class="mt-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Agent Performance Statistics</h2>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach ($stats['tickets'] as $status => $count)
                            <div class="p-4 bg-indigo-50 dark:bg-indigo-900 rounded-lg shadow">
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $status)) }}
                                    Tickets</p>
                                <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-300">{{ $count }}</p>
                            </div>
                        @endforeach
                        <div class="p-4 bg-blue-50 dark:bg-blue-900 rounded-lg shadow">
                            <p class="text-sm text-gray-700 dark:text-gray-300">Total Submitted Answers</p>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-300">{{ $stats['answers']['total'] }}</p>
                        </div>
                        <div class="p-4 bg-purple-50 dark:bg-purple-900 rounded-lg shadow">
                            <p class="text-sm text-gray-700 dark:text-gray-300">Accepted Answers</p>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-300">{{ $stats['answers']['accepted'] }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection