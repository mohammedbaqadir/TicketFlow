@props(['href', 'label', 'icon' => null, 'suffix' => null])

<li>
    <a href="{{ $href }}"
            {{ $attributes->merge(['class' => 'flex items-center py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white']) }}>
        @if($icon)
            {{ $icon }}
        @endif
        <span class="flex-grow">{{ $label }}</span>
        @if($suffix)
            {{ $suffix }}
        @endif
    </a>
</li>