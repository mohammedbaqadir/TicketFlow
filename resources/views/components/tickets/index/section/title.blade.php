@props(['title'])

<h2 {{ $attributes->merge(['class' => 'text-3xl font-bold text-gray-900 dark:text-white relative']) }}>
    <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-500 to-purple-500 dark:from-blue-300 dark:to-purple-300">
        {{$title}}
    </span>
</h2>