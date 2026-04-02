@props([
    'label' => '',
    'name' => '',
    'placeholder' => 'Pilih...',
    'icon' => null,
    'required' => false,
    'wireModel' => '',
    'wireModelModifier' => '',
    'value' => '',
    'disabled' => false,
    'options' => [],
    'optionValue' => 'id',
    'optionLabel' => 'name',
    'validatorMessage' => null,
    'error' => null,
    'hint' => null,
    'containerClass' => '',

    // Backward compatibility props
    'wrapperClass' => '',
    'labelClass' => 'label-text text-sm text-base-content/70',
    'selectClass' => '',
])

@php
    $fieldName = (string) $name;
    $fieldError = $error ?? ($fieldName !== '' ? $errors->first($fieldName) : null);
    $container = trim($containerClass.' '.$wrapperClass);
    $resolvedValue = old($fieldName, $value);
    $stateClass = $fieldError ? 'select-error' : '';
    $paddingClass = $icon ? 'pl-10' : '';
    $extraClass = trim($selectClass.' '.$stateClass.' '.$paddingClass);

    $isDynamicComponentIcon = is_string($icon) && (str_contains($icon, 'heroicon') || str_contains($icon, '::') || str_contains($icon, '.'));
@endphp

<fieldset class="{{ $container }}">
    @if ($label)
        <legend class="fieldset-legend {{ $labelClass }}">{{ $label }}</legend>
    @endif

    <div class="relative">
        @if ($icon)
            <div class="pointer-events-none absolute left-3 top-1/2 z-10 -translate-y-1/2">
                @if ($isDynamicComponentIcon)
                    <x-dynamic-component :component="$icon" class="h-4 w-4 opacity-70" />
                @else
                    <i data-lucide="{{ $icon }}" class="h-4 w-4 opacity-70"></i>
                @endif
            </div>
        @endif

        <select
            @if($fieldName !== '') name="{{ $fieldName }}" @endif
            @if($wireModel && $wireModelModifier)
                wire:model.{{ $wireModelModifier }}="{{ $wireModel }}"
            @elseif($wireModel)
                wire:model="{{ $wireModel }}"
            @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes->merge([
                'class' => trim('select select-bordered w-full rounded-xl '.$extraClass),
            ]) }}
        >
            @if($placeholder !== null && $placeholder !== '')
                <option value="">{{ $placeholder }}</option>
            @endif

            @if($slot->isEmpty())
                @foreach($options as $option)
                    @php
                        $optionVal = is_array($option) || is_object($option) ? data_get($option, $optionValue) : $option;
                        $optionText = is_array($option) || is_object($option) ? data_get($option, $optionLabel) : $option;
                    @endphp
                    <option value="{{ $optionVal }}" @selected((string) $resolvedValue === (string) $optionVal)>
                        {{ $optionText }}
                    </option>
                @endforeach
            @else
                {{ $slot }}
            @endif
        </select>
    </div>

    @if ($validatorMessage)
        <p class="validator-hint {{ $fieldError ? '' : 'hidden' }}">{{ $validatorMessage }}</p>
    @endif

    @if ($fieldError)
        <span class="mt-1 text-xs text-error">{{ $fieldError }}</span>
    @elseif ($hint)
        <span class="mt-1 text-xs text-base-content/55">{{ $hint }}</span>
    @endif
</fieldset>
