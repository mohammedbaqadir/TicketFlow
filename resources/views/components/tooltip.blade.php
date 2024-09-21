@props(['content', 'icon' => 'light-bulb'])
@php
    $tooltipId = 'tooltip-' . \Illuminate\Support\Str::random(8);
@endphp
<div class="relative inline-block">
    <div data-tooltip-target="{{ $tooltipId }}" data-tooltip-trigger="hover">
        {{ $slot }}
    </div>
    <div id="{{ $tooltipId }}" role="tooltip"
         class="absolute z-10 invisible inline-block px-4 py-2 text-sm font-medium transition-opacity duration-300 rounded-lg shadow-lg opacity-0 tooltip
                bg-sky-50 text-sky-900 dark:bg-indigo-800 dark:text-indigo-100 shadow-md
                border border-sky-300 dark:border-indigo-600
                max-w-xs">
        <div class="flex items-center space-x-2">
            <x-dynamic-component :component="'heroicon-o-' . $icon"
                                 class="w-4 h-4 flex-shrink-0 text-sky-600 dark:text-indigo-400" />
            <span class="inline-block leading-snug tooltip-content" data-unsanitized-content="{{ $content }}">
            </span>
        </div>
        <div class="tooltip-arrow" data-popper-arrow></div>
    </div>
</div>

<style>
    .tooltip-content {
        display: inline-block;
        max-width: 100%;
        word-wrap: break-word;
        overflow-wrap: break-word;
        hyphens: auto;
    }

    @media (min-width: 640px) {
        /* sm breakpoint */
        .tooltip-content {
            width: 20rem; /* Fixed width for multi-line content */
            max-width: 20rem; /* Approximately 5 words per line */
        }
    }

    /* Ensure the tooltip width is determined by its content */
    [role="tooltip"] {
        width: max-content;
        max-width: min(100vw - 2rem, 20rem); /* Responsive max-width */
    }

    /* Optimized for both single-line and multi-line content */
    @media (min-width: 640px) {
        [role="tooltip"] .tooltip-content {
            width: auto;
            max-width: 20rem; /* Maximum width */
            white-space: normal; /* Allow wrapping */
        }
    }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const tooltipContents = document.querySelectorAll('.tooltip-content[data-unsanitized-content]');
    tooltipContents.forEach(element => {
      const unsanitizedContent = element.getAttribute('data-unsanitized-content');
      const sanitizedContent = DOMPurify.sanitize(unsanitizedContent);
      element.innerHTML = sanitizedContent;
    });
  });
</script>