@extends('components.layout.app')

@section('content')
    <div class="container mx-auto text-center py-20">
        <h1 class="text-5xl font-bold mb-10 text-red-500">Oops! Something went wrong.</h1>
        <p class="text-lg mb-10 dark:text-white">We encountered an error while processing your request. Please try
                                                 again later.</p>
        <a href="{{ route('home') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Go to
                                                                                                                   Home</a>
    </div>
@endsection