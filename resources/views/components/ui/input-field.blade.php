@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'placeholder' => '',
    'icon' => null,
    'required' => false,
    'wireModel' => '',
    'wireModelModifier' => '',
    'value' => '',
    'disabled' => false,
    'readonly' => false,
    'maxlength' => null,
    'min' => null,
    'max' => null,
    'step' => null,
    'uppercase' => false,
    'lowercase' => false,
    'validatorMessage' => null,
    'error' => null,
    'hint' => null,
    'containerClass' => '',
    'size' => '',

    // Backward compatibility props
    'wrapperClass' => '',
    'labelClass' => 'label-text text-sm text-base-content/70',
    'inputClass' => '',
])

@php
    $fieldName = (string) $name;
    $fieldError = $error ?? ($fieldName !== '' ? $errors->first($fieldName) : null);
    $container = trim($containerClass.' '.$wrapperClass);

    $textTransformClass = $uppercase ? 'uppercase' : ($lowercase ? 'lowercase' : '');
    $inputStateClass = $fieldError ? 'input-error' : '';
    $sizeClass = trim($size.' '.$inputClass);

    $isDynamicComponentIcon = is_string($icon) && (str_contains($icon, 'heroicon') || str_contains($icon, '::') || str_contains($icon, '.'));
@endphp

<fieldset class="{{ $container }}">
    @if ($label)
        <legend class="fieldset-legend {{ $labelClass }}">{{ $label }}</legend>
    @endif

    <label class="input input-bordered validator flex w-full items-center gap-2 rounded-xl {{ $sizeClass }} {{ $inputStateClass }}">
        @if ($icon)
            @if ($isDynamicComponentIcon)
                <x-dynamic-component :component="$icon" class="h-4 w-4 opacity-70" />
            @else
                <i data-lucide="{{ $icon }}" class="h-4 w-4 opacity-70"></i>
            @endif
        @endif

        <input
            type="{{ $type }}"
            @if($fieldName !== '') name="{{ $fieldName }}" @endif
            @if($wireModel && $wireModelModifier)
                wire:model.{{ $wireModelModifier }}="{{ $wireModel }}"
            @elseif($wireModel)
                wire:model="{{ $wireModel }}"
            @endif
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($value !== '') value="{{ $value }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($maxlength) maxlength="{{ $maxlength }}" @endif
            @if($min !== null) min="{{ $min }}" @endif
            @if($max !== null) max="{{ $max }}" @endif
            @if($step) step="{{ $step }}" @endif
            {{ $attributes->merge([
                'class' => trim('grow '.$textTransformClass),
            ]) }}
        >

        {{ $slot }}
    </label>

    @if ($validatorMessage)
        <p class="validator-hint {{ $fieldError ? '' : 'hidden' }}">{{ $validatorMessage }}</p>
    @endif

    @if ($fieldError)
        <span class="mt-1 text-xs text-error">{{ $fieldError }}</span>
    @elseif ($hint)
        <span class="mt-1 text-xs text-base-content/55">{{ $hint }}</span>
    @endif
</fieldset>
