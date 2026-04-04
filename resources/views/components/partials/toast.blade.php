@php
    $hasToast = session()->has('success')
        || session()->has('error')
        || session()->has('warning')
        || session()->has('info');
@endphp

@if ($hasToast && ! request()->hasHeader('X-Livewire'))
    <div id="app-toast-stack" class="app-toast-stack" aria-live="polite" aria-atomic="true">
        @if (session()->has('success'))
            <x-ui.toast type="success" :message="session('success')" />
        @endif

        @if (session()->has('error'))
            <x-ui.toast type="error" :message="session('error')" />
        @endif

        @if (session()->has('warning'))
            <x-ui.toast type="warning" :message="session('warning')" />
        @endif

        @if (session()->has('info'))
            <x-ui.toast type="info" :message="session('info')" />
        @endif
    </div>
@endif