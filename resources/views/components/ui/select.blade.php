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
    'wrapperClass' => 'mb-3',
    'disabled' => false,
    'useOld' => true,
])

@php
    $selectId = $id ?? 'select-' . $name . '-' . uniqid();
    $current = $useOld ? old($name, $value) : $value;
@endphp

<div class="{{ $wrapperClass }}">
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

        @foreach ($options as $optionValue => $option)
            @php
                $optionLabel = is_array($option) ? ($option['label'] ?? '') : $option;
                $optionAttributes = is_array($option) ? ($option['attributes'] ?? []) : [];
            @endphp
            <option value="{{ $optionValue }}"
                @foreach ($optionAttributes as $attributeName => $attributeValue)
                    {{ $attributeName }}="{{ $attributeValue }}"
                @endforeach
                {{ (string) $current === (string) $optionValue ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>