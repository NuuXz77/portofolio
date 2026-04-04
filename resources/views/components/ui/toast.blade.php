@props([
    'type' => 'success',
    'message' => null,
])

@php
    $styles = [
        'success' => ['icon' => 'check-circle-2', 'label' => 'Success'],
        'error' => ['icon' => 'circle-alert', 'label' => 'Error'],
        'warning' => ['icon' => 'triangle-alert', 'label' => 'Warning'],
        'info' => ['icon' => 'info', 'label' => 'Info'],
    ];

    $state = $styles[$type] ?? $styles['info'];
    $resolvedType = isset($styles[$type]) ? $type : 'info';
    $normalizedMessage = trim((string) $message);
    $normalizedMessage = preg_replace('/\s+/', ' ', $normalizedMessage) ?? '';
    $signature = strtolower($resolvedType.'|'.$normalizedMessage);
@endphp

@if ($message)
    <div class="app-toast app-toast--{{ $resolvedType }}" data-app-toast data-toast-signature="{{ $signature }}" role="status" aria-live="polite" aria-atomic="true">
        <i data-lucide="{{ $state['icon'] }}" class="app-toast-icon h-5 w-5"></i>

        <div class="app-toast-content">
            <p class="app-toast-title">{{ $state['label'] }}</p>
            <p class="app-toast-message">{{ $message }}</p>
        </div>

        <button type="button" class="app-toast-close" data-toast-close aria-label="Dismiss notification">
            <i data-lucide="x" class="h-4 w-4"></i>
        </button>
    </div>
@endif
