@props([
    'label' => null,
    'name' => null,
    'error' => null,
    'hint' => null,
    'wrapperClass' => '',
    'labelClass' => 'label-text text-sm text-base-content/70',
    'inputClass' => '',
])

@php
    $fieldError = $error ?? ($name ? $errors->first($name) : null);
@endphp

<label class="form-control {{ $wrapperClass }}">
    @if ($label)
        <span class="{{ $labelClass }}">{{ $label }}</span>
    @endif

    <input
        type="file"
        @if($name) name="{{ $name }}" @endif
        {{ $attributes->merge([
            'class' => trim('file-input file-input-bordered rounded-xl '.$inputClass),
        ]) }}
    >

    @if ($fieldError)
        <span class="mt-1 text-xs text-error">{{ $fieldError }}</span>
    @elseif ($hint)
        <span class="mt-1 text-xs text-base-content/55">{{ $hint }}</span>
    @endif
</label>
