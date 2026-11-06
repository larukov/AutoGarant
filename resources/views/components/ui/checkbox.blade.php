@props([
    'name',
    'value' => null,
    'label' => null,
    'checked' => false,
    'id' => null,
    'class' => '',
    'labelClass' => '',
    'disabled' => false,
])

@php
    $checkboxId = $id ?? 'checkbox-' . $name . '-' . uniqid();
@endphp

<input type="checkbox" name="{{ $name }}" id="{{ $checkboxId }}" value="{{ $value }}"
    class="form-check-input @error($name) is-invalid @enderror {{ $class }}" @if ($checked) checked @endif
   @if ($disabled) disabled @endif {{ $attributes->except(['class', 'label', 'labelClass', 'value', 'checked']) }}>

@if ($label)
    <label class="form-check-label {{ $labelClass }}" for="{{ $checkboxId }}">
        {{ $label }}
    </label>
@endif