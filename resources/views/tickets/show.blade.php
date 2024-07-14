@extends('components.layout.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-gradient-to-r from-gray-100 to-purple-100 dark:from-gray-600 dark:to-gray-400 shadow-xl rounded-lg overflow-hidden">
            <x-tickets.show.header :ticket="$ticket" />

            <div class="px-6 pb-6">
                <x-tickets.show.answers :ticket="$ticket" />
            </div>
        <x-tickets.show.footer :ticket="$ticket" />
    </div>
@endsection