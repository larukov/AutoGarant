@props([
    'name',
    'label' => null,
    'options' => [],
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'id' => null,
    'class' => '',
    'labelClass' => 'form-label small fw-bold text-muted',
    'disabled' => false,
])

@php
    $selectId = $id ?? 'select-' . $name . '-' . uniqid();
    $current = old($name, $value);
@endphp

<div class="mb-3">
    @if ($label)
        <label for="{{ $selectId }}" class="{{ $labelClass }}">
            {{ $label }}
            @if ($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <select name="{{ $name }}" id="{{ $selectId }}"
        class="form-select @error($name) is-invalid @enderror {{ $class }}"
        @if ($required) required @endif @if ($disabled) disabled @endif
        {{ $attributes->except(['class', 'label', 'labelClass']) }}>

        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}"
                {{ (string) $current === (string) $optionValue ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>