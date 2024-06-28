@props(['image', 'name', 'message', 'time', 'iconClass', 'icon'])

<a href="#" class="flex py-3 px-4 hover:bg-gray-100 dark:hover:bg-gray-600">
    <div class="flex-shrink-0 relative">
        <img class="w-11 h-11 rounded-full" src="{{ $image }}" alt="{{ $name }} avatar" />
        <div class="flex absolute justify-center items-center ml-6 -mt-5 w-5 h-5 rounded-full border border-white {{ $iconClass }} dark:border-gray-700">
            <x-dynamic-component :component="'heroicon-s-' . $icon" class="w-3 h-3 text-white" aria-hidden="true" />
        </div>
    </div>
    <div class="pl-3 w-full">
        <div class="text-gray-500 font-normal text-sm mb-1.5 dark:text-gray-400">
            <span class="font-semibold text-gray-900 dark:text-white">{{ $name }}</span> {!! $message !!}
        </div>
        <div class="text-xs font-medium text-primary-600 dark:text-primary-500">{{ $time }}</div>
    </div>
</a>