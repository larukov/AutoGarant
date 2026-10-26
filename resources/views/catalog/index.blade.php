@extends('layouts.app')

@section('title', 'Каталог автомобилей — Автосалон «Автогарант»')

@section('content')
    <div class="container py-5">
        <!-- Заголовок раздела -->
        <div class="row mb-4">
            <div class="col">
                <h1 class="fw-bold text-dark mb-2">Каталог автомобилей</h1>
                <p class="text-muted">Используйте фильтры для точного подбора транспортного средства под ваши критерии</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Левая колонка: Форма параметрического поиска и фильтрации -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm rounded-3 p-4 bg-white sticky-lg-top" style="top: 2rem; z-index: 10;">
                    <h5 class="fw-bold text-dark mb-4 d-flex align-items-center justify-content-between">
                        <span>Фильтры</span>
                        <a href="{{ route('catalog.index') }}" class="text-muted small text-decoration-none fw-normal"
                            style="font-size: 0.8rem;">Сбросить</a>
                    </h5>

                    <form action="{{ route('catalog.index') }}" method="GET">
                        <!-- Фильтр по Маркам (Брендам) -->
                        <div class="mb-3">
                            <label for="brand" class="form-label small fw-bold text-secondary">Марка автомобиля</label>
                            <select class="form-select form-select-sm" id="brand" name="brand_id">
                                <option value="">Все марки</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Фильтр по Типу кузова -->
                        <div class="mb-3">
                            <label for="body_type" class="form-label small fw-bold text-secondary">Тип кузова</label>
                            <select class="form-select form-select-sm" id="body_type" name="body_type_id">
                                <option value="">Все типы</option>
                                @foreach($bodyTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('body_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Фильтр по Коробке передач (ENUM из БД) -->
                        <div class="mb-3">
                            <label for="transmission" class="form-label small fw-bold text-secondary">Коробка
                                передач</label>
                            <select class="form-select form-select-sm" id="transmission" name="transmission">
                                <option value="">Любая КПП</option>
                                <option value="manual" {{ request('transmission') == 'manual' ? 'selected' : '' }}>Механика
                                </option>
                                <option value="automatic" {{ request('transmission') == 'automatic' ? 'selected' : '' }}>
                                    Автомат</option>
                                <option value="robot" {{ request('transmission') == 'robot' ? 'selected' : '' }}>Робот
                                </option>
                            </select>
                        </div>

                        <!-- Фильтр по Году выпуска -->
                        <div class="mb-3">
                            <label for="year" class="form-label small fw-bold text-secondary">Год выпуска</label>
                            <select class="form-select form-select-sm" id="year" name="year">
                                <option value="">Любой год</option>
                                @for($y = 2026; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }} г.</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Фильтр по Максимальной Цене -->
                        <div class="mb-4">
                            <label for="price_max" class="form-label small fw-bold text-secondary">Цена до (₽)</label>
                            <input type="number" class="form-control form-control-sm" id="price_max" name="price_max"
                                value="{{ request('price_max') }}" placeholder="Например: 3000000">
                        </div>

                        <!-- Кнопка применения фильтра -->
                        <button type="submit" class="btn btn-primary btn-sm w-100 py-2 fw-bold shadow-sm rounded-2">
                            Применить фильтр
                        </button>
                    </form>
                </div>
            </div>

            <!-- Правая колонка: Сетка вывода автомобилей -->
            <div class="col-lg-9">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                    @forelse($cars as $car)
                        <div class="col d-flex">
                            <div
                                class="card w-100 shadow-sm border-0 rounded-3 overflow-hidden h-100 d-flex flex-column justify-content-between">

                                <!-- Изображение автомобиля -->
                                <div class="position-relative bg-light" style="height: 180px;">
                                    <img src="{{ $car->images->first()->image_url ?? '/images/default-car.jpg' }}"
                                        alt="{{ $car->model->brand->name }} {{ $car->model->name }}"
                                        class="w-100 h-100 object-cover">

                                    <!-- статус -->
                                    <span
                                        class="position-absolute top-0 start-0 m-3 badge py-2 px-3 font-weight-bold
                                            @if($car->status === 'available') bg-success 
                                            @elseif($car->status === 'reserved') bg-warning text-dark 
                                            @else bg-danger @endif">
                                        {{ $car->status === 'available' ? 'В наличии' : ($car->status === 'reserved' ? 'Забронирован' : 'Продан') }}
                                    </span>
                                </div>

                                <!-- Тело карточки -->
                                <div class="card-body d-flex flex-column justify-content-between p-4">
                                    <div>
                                        <h5 class="card-title fw-bold text-dark mb-1">
                                            {{ $car->model->brand->name }} {{ $car->model->name }}
                                        </h5>
                                        <p class="text-muted small mb-3">{{ $car->bodyType->name }} • {{ $car->year }} г.в.</p>

                                        <!-- Краткие тех-характеристики из полей таблицы cars -->
                                        <div class="row g-2 pt-2 pb-3 my-2 border-top border-bottom text-secondary small">
                                            <div class="col-6">Объем: <strong>{{ number_format($car->engine_volume, 1) }}
                                                    л</strong></div>
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
                                        <div class="d-flex flex-col">
                                            <span class="text-muted text-uppercase small font-weight-bold"
                                                style="font-size: 0.7rem;">Стоимость</span>
                                            <span class="fs-5 fw-bold text-dark">{{ number_format($car->price, 0, ',', ' ') }}
                                                ₽</span>
                                        </div>
                                        <a href="{{ route('catalog.show', $car->id) }}"
                                            class="btn btn-dark btn-sm rounded-2 px-3 py-2 fw-semibold">
                                            Подробнее
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @empty
                        <!-- Блок отображения, если по фильтрам ничего не найдено -->
                        <div class="col-112 w-100 py-5 text-center bg-white rounded-3 shadow-sm">
                            <div class="py-4 text-muted">
                                <span class="fs-2 block mb-2">🔍</span>
                                <h5 class="fw-bold">Ничего не найдено</h5>
                                <p class="small mb-0">Попробуйте изменить параметры поиска или сбросить активные фильтры.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{ $cars->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection