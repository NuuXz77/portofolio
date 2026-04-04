@props([
    'label' => '',
    'name' => '',
    'rows' => 4,
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'maxlength' => null,
    'minlength' => null,
    'error' => null,
    'hint' => null,
    'containerClass' => '',

    // Backward compatibility props
    'wrapperClass' => '',
    'labelClass' => 'label-text text-sm text-base-content/70',
    'textareaClass' => '',
])

@php
    $fieldName = (string) $name;
    $fieldError = $error ?? ($fieldName !== '' ? $errors->first($fieldName) : null);
    $container = trim($containerClass.' '.$wrapperClass);
    $stateClass = $fieldError ? 'textarea-error' : '';
@endphp

<fieldset class="{{ $container }}">
    @if ($label)
        <legend class="fieldset-legend {{ $labelClass }}">{{ $label }}</legend>
    @endif

    <textarea
        rows="{{ $rows }}"
        @if($fieldName !== '') name="{{ $fieldName }}" @endif
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        @if($maxlength) maxlength="{{ $maxlength }}" @endif
        @if($minlength) minlength="{{ $minlength }}" @endif
        {{ $attributes->merge([
            'class' => trim('textarea textarea-bordered glass-field w-full rounded-xl '.$textareaClass.' '.$stateClass),
        ]) }}
    ></textarea>

    @if ($fieldError)
        <span class="mt-1 text-xs text-error">{{ $fieldError }}</span>
    @elseif ($hint)
        <span class="mt-1 text-xs text-base-content/55">{{ $hint }}</span>
    @endif
</fieldset>
