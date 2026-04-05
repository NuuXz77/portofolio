@props([
    'align' => 'end',
    'icon' => 'more-vertical',
])

<div class="dropdown dropdown-{{ $align }}" data-dropdown-action>
    <button tabindex="0" type="button" class="btn btn-ghost btn-xs btn-circle text-base-content/80 hover:text-base-content" data-dropdown-trigger aria-expanded="false" aria-label="Open row actions">
        <i data-lucide="{{ $icon }}" class="h-4 w-4"></i>
    </button>

    <ul tabindex="0" class="glass-surface-soft menu dropdown-content z-90 mt-1 w-40 rounded-box border border-base-content/10 bg-base-100 p-2 shadow-xl" data-dropdown-menu>
        {{ $slot }}
    </ul>
</div>
