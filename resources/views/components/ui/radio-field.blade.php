@props([
    'label' => '',
    'name' => null,
    'value' => null,
    'error' => null,
    'wrapperClass' => '',
    'radioClass' => '',
])

@php
    $fieldError = $error ?? ($name ? $errors->first($name) : null);
@endphp

<div class="form-control {{ $wrapperClass }}">
    <label class="label cursor-pointer justify-start gap-2">
        <input
            type="radio"
            @if($name) name="{{ $name }}" @endif
            @if(!is_null($value)) value="{{ $value }}" @endif
            {{ $attributes->merge([
                'class' => trim('radio radio-info radio-sm '.$radioClass),
            ]) }}
        >
        <span class="label-text text-sm text-base-content/75">{{ $label }}</span>
    </label>

    @if ($fieldError)
        <span class="mt-1 text-xs text-error">{{ $fieldError }}</span>
    @endif
</div>
