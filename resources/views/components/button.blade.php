@props([
    'type' => 'button',
    'variant' => 'primary',
])

@php
    $variantClasses = [
        'primary' => 'bg-blue-600 hover:bg-blue-700 text-white',
        'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-800',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white',
    ][$variant];
@endphp

<button
        {{ $attributes->merge(['type' => $type, 'class' => "px-4 py-2 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 {$variantClasses}"]) }}
>
    {{ $slot }}
</button>