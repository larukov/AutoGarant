@props([
    'name',
    'type' => 'text',
    'label' => null,
    'placeholder' => '',
    'value' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'autocomplete' => null,
    'id' => null,
])

@php
    $inputId = $id ?? 'input-' . $name . '-' . uniqid();
    $hasError = $errors->has($name);
    $oldValue = old($name, $value ?? '');
@endphp

<div class="mb-3">
    @if ($label)
        <label for="{{ $inputId }}" class="form-label">
            {{ $label }}
            @if ($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <input type="{{ $type }}" name="{{ $name }}" id="{{ $inputId }}"
        class="form-control @error($name) is-invalid @enderror {{ $attributes->get('class', '') }}"
        placeholder="{{ $placeholder }}" value="{{ $oldValue }}" @if ($required) required @endif
        @if ($disabled) disabled @endif @if ($readonly) readonly @endif
        @if ($autocomplete) autocomplete="{{ $autocomplete }}" @else autocomplete="off" @endif
        {{ $attributes->except(['class']) }}>

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
