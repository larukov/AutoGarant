@php
    $car = $car ?? null;
    $selectedBrandId = old('brand_id', $car?->model->brand_id);
    $selectedModelId = old('model_id', $car?->model_id);
    $selectedOptionIds = old('options', $optionIds ?? []);
@endphp

@csrf

@if($errors->any())
    <x-validation-errors class="mb-4" />
@endif

<input type="hidden" name="status" value="{{ old('status', $car->status ?? 'available') }}">

<div class="row g-3">
    {{-- Марка и модель --}}
    <div class="col-12 col-md-6">
        <label class="form-label small fw-bold text-muted">Марка автомобиля</label>
        <select name="brand_id" id="brandSelect" class="form-select form-select-sm" required>
            <option value="">Выберите марку</option>
            @foreach($brands as $brand)
                <option value="{{ $brand->id }}" {{ (int) $selectedBrandId === (int) $brand->id ? 'selected' : '' }}>
                    {{ $brand->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label small fw-bold text-muted">Модель автомобиля</label>
        <select name="model_id" id="modelSelect" class="form-select form-select-sm" required>
            <option value="">Сначала выберите марку</option>
            @foreach($brands as $brand)
                @foreach($brand->models as $model)
                    <option value="{{ $model->id }}" data-brand="{{ $brand->id }}" {{ (int) $selectedModelId === (int) $model->id ? 'selected' : '' }}>
                        {{ $model->name }}
                    </option>
                @endforeach
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-6">
        <x-ui.select name="body_type_id" label="Тип кузова" :options="$bodyTypes->pluck('name', 'id')"
            :value="$car->body_type_id ?? ''" :required="true" class="form-select-sm" />
    </div>

    <div class="col-6 col-md-3">
        <label class="form-label small fw-bold text-muted">Год выпуска</label>
        <input type="number" name="year" min="1900" max="{{ date('Y') + 1 }}" class="form-control form-control-sm"
            value="{{ old('year', $car->year ?? date('Y')) }}" required>
    </div>
    <div class="col-6 col-md-3">
        <label class="form-label small fw-bold text-muted">Объем двигателя (л)</label>
        <input type="number" name="engine_volume" min="0.1" max="99.9" step="0.1" class="form-control form-control-sm"
            placeholder="2.0" value="{{ old('engine_volume', $car->engine_volume ?? '') }}" required>
        <div class="form-text text-danger d-none" id="engineVolumeError" style="font-size: 0.7rem;">
            Допустимо от 0.1 до 99.9 л (не более двух знаков).
        </div>
    </div>

    <div class="col-12">
        <label class="form-label small fw-bold text-muted">Стоимость (₽)</label>
        <input type="number" name="price" min="0" step="0.01" class="form-control form-control-sm"
            placeholder="Цена в рублях" value="{{ old('price', $car->price ?? '') }}" required>
    </div>

    <div class="col-12 col-md-4">
        <x-ui.select name="transmission" label="Коробка передач" :options="[
        'manual' => 'Механическая (МКПП)',
        'automatic' => 'Автоматическая (АКПП)',
        'robot' => 'Роботизированная',
    ]" :value="$car->transmission ?? ''"
            :required="true" class="form-select-sm" />
    </div>
    <div class="col-12 col-md-4">
        <x-ui.select name="drive_type" label="Привод" :options="[
        'FWD' => 'Передний (FWD)',
        'RWD' => 'Задний (RWD)',
        'AWD' => 'Полный (AWD)',
    ]" :value="$car->drive_type ?? ''" :required="true" class="form-select-sm" />
    </div>
    <div class="col-12 col-md-4">
        <x-ui.select name="fuel_type" label="Топливо" :options="[
        'petrol' => 'Бензин',
        'diesel' => 'Дизель',
        'electric' => 'Электро',
        'hybrid' => 'Гибрид',
    ]" :value="$car->fuel_type ?? ''" :required="true" class="form-select-sm" />
    </div>
</div>

<hr>
<h6 class="fw-bold text-dark mb-3">Дополнительные опции</h6>
<div class="row g-2">
    @forelse($options as $option)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="form-check border rounded p-2 px-3">
                <input class="form-check-input" type="checkbox" name="options[]" value="{{ $option->id }}"
                    id="option{{ $option->id }}" {{ in_array($option->id, (array) $selectedOptionIds) ? 'checked' : '' }}>
                <label class="form-check-label small w-100" for="option{{ $option->id }}">
                    {{ $option->name }}
                    <span class="text-muted d-block" style="font-size: 0.75rem;">+ <x-money
                            :money="$option->price" /></span>
                </label>
            </div>
        </div>
    @empty
        <div class="col-12 text-muted small">Список опций пуст.</div>
    @endforelse
</div>

<hr>
<h6 class="fw-bold text-dark mb-3">Фотографии автомобиля</h6>
<div class="mb-1">
    <input type="file" name="images[]" class="form-control form-control-sm" accept="image/*" multiple>
    <div class="form-text small text-muted">Можно выбрать несколько изображений (JPG, PNG, WEBP, до 5 МБ каждое).</div>
</div>

@if($car && $car->images->count() > 0)
    <div class="row g-2 mt-2">
        @foreach($car->images as $image)
            <div class="col-4 col-md-3">
                <div class="border rounded overflow-hidden position-relative">
                    <img src="{{ asset($image->image_url) }}" alt="{{ $image->name }}"
                        style="width: 100%; height: 80px; object-fit: cover;">
                    <form action="{{ route('admin.cars.images.destroy', $image->id) }}" method="POST"
                        onsubmit="return confirm('Удалить это изображение?');">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="car_id" value="{{ $car->id }}">
                        <button type="submit" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                            title="Удалить фото">✕</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif

<div class="mt-4">
    <button type="submit" class="btn btn-success fw-bold px-4 py-2">
        {{ isset($car) ? 'Сохранить изменения' : 'Добавить автомобиль' }}
    </button>
    <a href="{{ route('admin.cars.index') }}" class="btn btn-outline-secondary">Отмена</a>
</div>

<script>
    const brandSelect = document.getElementById('brandSelect');
    const modelSelect = document.getElementById('modelSelect');

    function filterModels() {
        const brandId = brandSelect ? brandSelect.value : '';
        let hasSelected = false;
        Array.from(modelSelect.options).forEach(opt => {
            if (!opt.dataset.brand) return;
            const match = opt.dataset.brand === brandId;
            opt.hidden = !match;
            if (match && opt.selected) hasSelected = true;
        });
        if (!hasSelected) modelSelect.selectedIndex = 0;
    }

    if (brandSelect && modelSelect) {
        brandSelect.addEventListener('change', filterModels);
        filterModels();
    }

    const engineInput = document.querySelector('[name="engine_volume"]');
    const engineError = document.getElementById('engineVolumeError');
    if (engineInput && engineError) {
        engineInput.addEventListener('input', function () {
            const val = parseFloat(this.value);
            const invalid = isNaN(val) || val < 0.1 || val > 99.9;
            engineError.classList.toggle('d-none', !invalid);
            this.classList.toggle('is-invalid', invalid);
        });
    }
</script>