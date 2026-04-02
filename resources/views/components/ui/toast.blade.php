@props([
    'type' => 'success',
    'message' => null,
])

@php
    $styles = [
        'success' => ['alert' => 'alert-success', 'icon' => 'check-circle-2'],
        'error' => ['alert' => 'alert-error', 'icon' => 'circle-alert'],
        'warning' => ['alert' => 'alert-warning', 'icon' => 'triangle-alert'],
        'info' => ['alert' => 'alert-info', 'icon' => 'info'],
    ];

    $state = $styles[$type] ?? $styles['info'];
@endphp

@if ($message)
    <div class="toast toast-top toast-end z-50">
        <div class="alert {{ $state['alert'] }} shadow-lg">
            <i data-lucide="{{ $state['icon'] }}" class="h-5 w-5"></i>
            <span>{{ $message }}</span>
        </div>
    </div>
@endif
