@props([
    'align' => 'end',
    'icon' => 'more-vertical',
])

<div class="dropdown dropdown-{{ $align }}">
    <button tabindex="0" type="button" class="btn btn-ghost btn-xs btn-circle" aria-label="Open row actions">
        <i data-lucide="{{ $icon }}" class="h-4 w-4"></i>
    </button>

    <ul tabindex="0" class="menu dropdown-content z-50 mt-1 w-40 rounded-box border border-base-content/10 bg-base-100 p-2 shadow-xl">
        {{ $slot }}
    </ul>
</div>
