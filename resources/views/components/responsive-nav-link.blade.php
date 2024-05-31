<!-- resources/views/components/responsive-nav-link.blade.php -->
@props(['active'])

@php
    $classes = ($active ?? false)
                ? 'block w-full pl-3 pr-4 py-2 border-l-4 border-indigo-400 text-base font-medium text-gray-100 bg-indigo-900 focus:outline-none focus:text-indigo-200 focus:bg-indigo-900 focus:border-indigo-700 transition duration-150 ease-in-out'
                : 'block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-gray-100 hover:bg-gray-700 focus:outline-none focus:text-gray-100 focus:bg-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>