@props([
    // New API
    'modalId' => 'modal_default',
    'buttonText' => 'Tambah Data',
    'buttonIcon' => 'plus',
    'buttonClass' => 'btn btn-primary btn-sm gap-2',
    'buttonHiddenText' => true,
    'buttonBadge' => null,
    'buttonBadgeClass' => 'badge badge-secondary badge-sm absolute -top-1 -right-1',
    'saveButtonText' => 'Simpan',
    'saveButtonIcon' => 'check',
    'saveButtonClass' => 'btn btn-primary gap-2 btn-sm',
    'saveAction' => 'save',
    'showButton' => false,
    'showSaveButton' => true,
    'modalSize' => 'modal-box',
    'mode' => 'auto',

    // Legacy API
    'open' => false,
    'title' => null,
    'maxWidth' => 'max-w-2xl',
    'closeAction' => null,
    'bodyClass' => '',
])

@php
    $isLegacy = $mode === 'legacy' || ($mode === 'auto' && ($closeAction !== null || $open));

    $resolveIcon = function (?string $icon, string $fallback): array {
        if (! $icon) {
            return ['type' => 'none', 'value' => null];
        }

        if (str_contains($icon, 'heroicon')) {
            return ['type' => 'lucide', 'value' => $fallback];
        }

        if (str_contains($icon, '::') || str_contains($icon, '.')) {
            return ['type' => 'component', 'value' => $icon];
        }

        return ['type' => 'lucide', 'value' => $icon];
    };

    $buttonIconConfig = $resolveIcon($buttonIcon, 'plus');
    $saveIconConfig = $resolveIcon($saveButtonIcon, 'check');
@endphp

@if ($isLegacy)
    @if ($open)
        <div class="modal modal-open">
            <div class="glass-card modal-box {{ $maxWidth }} rounded-2xl {{ $bodyClass }}">
                @if ($title)
                    <h3 class="text-lg font-semibold text-white">{{ $title }}</h3>
                @endif

                {{ $slot }}

                @isset($footer)
                    <div class="modal-action">
                        {{ $footer }}
                    </div>
                @endisset
            </div>

            @if ($closeAction)
                <div class="modal-backdrop" wire:click="{{ $closeAction }}"></div>
            @else
                <div class="modal-backdrop"></div>
            @endif
        </div>
    @endif
@else
    <div
        x-data="{
            openModal() { this.$refs.modal?.showModal(); },
            closeModal() { this.$refs.modal?.close(); }
        }"
        x-on:open-modal.window="if ($event.detail?.id === '{{ $modalId }}') openModal()"
    >
        @if($showButton)
            <button type="button" class="relative {{ $buttonClass }}" @click="openModal()">
                @if($buttonIconConfig['type'] === 'component')
                    <x-dynamic-component :component="$buttonIconConfig['value']" class="h-5 w-5" />
                @elseif($buttonIconConfig['type'] === 'lucide')
                    <i data-lucide="{{ $buttonIconConfig['value'] }}" class="h-5 w-5"></i>
                @endif

                <span class="{{ $buttonHiddenText ? 'hidden sm:inline' : '' }}">{{ $buttonText }}</span>

                @if($buttonBadge !== null && $buttonBadge !== '')
                    <span class="{{ $buttonBadgeClass }}">{{ $buttonBadge }}</span>
                @endif
            </button>
        @endif

        @teleport('body')
            <dialog x-ref="modal" id="{{ $modalId }}" class="modal backdrop-blur-sm" wire:ignore.self>
                <div class="glass-card {{ $modalSize }} rounded-2xl border border-base-300">
                    <form method="dialog">
                        <button type="submit" class="btn btn-circle btn-ghost btn-sm absolute right-2 top-2" @click="closeModal()">✕</button>
                    </form>

                    @if ($title)
                        <h3 class="mb-4 text-lg font-bold">{{ $title }}</h3>
                    @endif

                    <form wire:submit.prevent="{{ $saveAction }}">
                        <div class="py-4">
                            {{ $slot }}
                        </div>

                        @isset($footer)
                            <div class="mt-6 flex justify-end gap-3">
                                {{ $footer }}
                            </div>
                        @else
                            @if($showSaveButton)
                                <div class="mt-6 flex justify-end gap-3">
                                    <button
                                        type="submit"
                                        class="{{ $saveButtonClass }}"
                                        wire:loading.attr="disabled"
                                        wire:target="{{ $saveAction }}"
                                    >
                                        <span wire:loading.remove wire:target="{{ $saveAction }}" class="flex items-center gap-2">
                                            @if($saveIconConfig['type'] === 'component')
                                                <x-dynamic-component :component="$saveIconConfig['value']" class="h-5 w-5" />
                                            @elseif($saveIconConfig['type'] === 'lucide')
                                                <i data-lucide="{{ $saveIconConfig['value'] }}" class="h-5 w-5"></i>
                                            @endif

                                            {{ $saveButtonText }}
                                        </span>
                                        <span wire:loading wire:target="{{ $saveAction }}" class="flex items-center gap-2">
                                            <span class="loading loading-spinner loading-sm"></span>
                                            Memproses...
                                        </span>
                                    </button>
                                </div>
                            @endif
                        @endisset
                    </form>
                </div>
            </dialog>
        @endteleport
    </div>
@endif
