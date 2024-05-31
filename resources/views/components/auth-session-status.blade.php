<!-- resources/views/components/auth-session-status.blade.php -->
@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-400']) }}>
        {{ $status }}
    </div>
@endif