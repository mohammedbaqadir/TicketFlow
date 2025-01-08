@php use App\Config\TicketConfig; @endphp
@props(['priority', 'label' ])

<span class="inline-flex items-center px-2 py-1 rounded-full text-xs sm:text-sm font-medium {{
TicketConfig::getBadgeStyleForPriority($priority) }}">
                <x-dynamic-component :component="TicketConfig::getIconForPriority($priority)"
                                     class='w-4 h-4 sm:w-5 sm:h-5 mr-1' />
                {{ $label }}
</span>