@props(['description'])

<div class="text-lg lg:text-xl font-medium text-gray-600 dark:text-gray-300 leading-relaxed tracking-wide">
    {!!Str::markdown($description, [
    'html_input' => 'strip',
])!!}
</div>