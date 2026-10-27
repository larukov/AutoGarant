@props(['car'])

<div class="card w-100 shadow-sm border-0 rounded-3 overflow-hidden h-100 d-flex flex-column justify-content-between">
    <!-- Изображение из связанной таблицы images -->
    <div class="position-relative bg-secondary" style="height: 220px;">
        <img src="{{ $car->images->first()->image_url ?? '/images/default-car.jpg' }}"
            alt="{{ $car->model->brand->name }} {{ $car->model->name }}" class="w-100 h-100 object-cover">

        <!-- Статус автомобиля (из ENUM поля 'status' в таблице cars) -->
        <span class="position-absolute top-0 start-0 m-3 badge font-weight-bold py-2 px-3 
            @if($car->status === 'available') bg-success 
            @elseif($car->status === 'reserved') bg-warning text-dark 
            @else bg-danger @endif">
            {{ $car->status === 'available' ? 'В наличии' : ($car->status === 'reserved' ? 'Забронирован' : 'Продан') }}
        </span>
    </div>

    <!-- Контент карточки -->
    <div class="card-body d-flex flex-column justify-content-between p-4">
        <div>
            <h5 class="card-title fw-bold text-dark mb-1">
                {{ $car->model->brand->name }} {{ $car->model->name }}
            </h5>
            <p class="text-muted small mb-3">{{ $car->bodyType->name }} • {{ $car->year }} г.в.</p>

            <!-- Краткие тех-характеристики из полей таблицы cars -->
            <div class="row g-2 pt-2 pb-3 my-2 border-top border-bottom text-secondary small">
                <div class="col-6">Объем: <strong>{{ number_format($car->engine_volume, 1) }} л</strong></div>
                <div class="col-6">КПП: <strong>
                        @if($car->transmission === 'manual') Механика
                        @elseif($car->transmission === 'automatic') Автомат
                        @else Робот @endif
                    </strong></div>
                <div class="col-6">Привод: <strong>{{ $car->drive_type }}</strong></div>
                <div class="col-6">Топливо: <strong>
                        @if($car->fuel_type === 'petrol') Бензин
                        @elseif($car->fuel_type === 'diesel') Дизель
                        @elseif($car->fuel_type === 'electric') Электро
                        @else Гибрид @endif
                    </strong></div>
            </div>
        </div>

        <!-- Цена и Кнопка перехода к карточке -->
        <div class="d-flex align-items-center justify-content-between pt-3 mt-auto">
            <div class="d-flex flex-column">
                <span class="text-muted text-uppercase small"
                    style="font-size: 0.7rem;">Стоимость</span>
                <span class="fs-5 fw-bold text-dark">{{ number_format($car->price, 0, ',', ' ') }} ₽</span>
            </div>
            <a href="{{ route('catalog.show', $car->id) }}" class="btn btn-dark btn-sm rounded-2 px-3 py-2 fw-semibold">
                Подробнее
            </a>
        </div>
    </div>
</div>