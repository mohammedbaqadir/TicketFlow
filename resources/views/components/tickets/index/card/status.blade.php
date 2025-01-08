@php use App\Config\TicketConfig; @endphp
@props(['status'])

<span class="inline-flex items-center px-2 py-1 rounded-full text-xs sm:text-sm font-medium {{ TicketConfig::getBadgeStyleForStatus($status) }}">
    <x-dynamic-component :component="TicketConfig::getIconForStatus($status)" class="w-3 h-3 sm:w-4 sm:h-4 mr-1" />
    {{ TicketConfig::getStatusLabel($status) }}
</span>