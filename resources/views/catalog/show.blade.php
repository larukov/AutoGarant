@extends('layouts.app')

@section('title', $car->model->brand->name . ' ' . $car->model->name . ' (' . $car->year . ' г.в.) — Автосалон')

@section('content')
<div class="container py-5">
    <!-- Навигационная цепочка (Хлебные крошки) -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('catalog.index') }}" class="text-decoration-none">Каталог</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $car->model->brand->name }} {{ $car->model->name }}</li>
        </ol>
    </nav>

    {{-- Вывод сообщений об успешном бронировании или ошибках --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Левая колонка: Галерея изображений автомобиля -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white p-3">
                <!-- Главное большое изображение -->
                <div class="main-image-container bg-light rounded mb-3" style="height: 450px;">
                    <img src="{{ $car->images->first()->image_url ?? '/images/default-car.jpg' }}" 
                         id="mainCarImage"
                         alt="{{ $car->model->brand->name }} {{ $car->model->name }}" 
                         class="w-100 h-100 object-cover rounded">
                </div>

                <!-- Миниатюры -->
                @if($car->images->count() > 1)
                    <div class="row g-2 overflow-auto flex-nowrap px-1" style="max-height: 100px;">
                        @foreach($car->images as $image)
                            <div class="col-3" style="cursor: pointer; min-width: 100px;">
                                <img src="{{ $image->image_url }}" 
                                     alt="Миниатюра" 
                                     class="img-thumbnail w-100 h-100 object-cover"
                                     onclick="document.getElementById('mainCarImage').src = this.src">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Правая колонка: Информация, Цена и Кнопка бронирования -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 p-4 bg-white sticky-lg-top" style="top: 2rem; z-index: 10;">
                
                <!-- Статус авто (из ENUM поля 'status') -->
                <div class="mb-2">
                    <span class="badge py-2 px-3 fw-bold 
                        @if($car->status === 'available') bg-success 
                        @elseif($car->status === 'reserved') bg-warning text-dark 
                        @else bg-danger @endif">
                        {{ $car->status === 'available' ? 'В наличии на складе' : ($car->status === 'reserved' ? 'Забронирован' : 'Продан') }}
                    </span>
                </div>

                <!-- Название марки и модели -->
                <h1 class="fw-bold text-dark display-6 mb-1">
                    {{ $car->model->brand->name }} {{ $car->model->name }}
                </h1>
                <p class="text-muted fs-5 mb-4">{{ $car->bodyType->name }} • {{ $car->year }} год выпуска</p>

                <!-- Фиксация цены -->
                <div class="p-3 bg-light rounded-3 mb-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted small fw-bold d-block" style="font-size: 0.75rem; letter-spacing: 0.5px;">Актуальная стоимость</span>
                        <span class="display-6 fw-bold text-dark">{{ number_format($car->price, 0, ',', ' ') }} ₽</span>
                    </div>
                    <span class="text-success small fw-semibold">Цена зафиксирована</span>
                </div>

                <!-- Кнопка вызова формы резервирования / Условия доступности -->
                @if($car->status === 'available')
                    @auth
                        <button type="button" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow-sm rounded-3 mb-3" data-bs-toggle="modal" data-bs-target="#orderReservationModal">
                            Зарезервировать онлайн
                        </button>
                    @else
                        <button type="button" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow-sm rounded-3 mb-3" data-bs-toggle="modal" data-bs-target="#AuthorizationModal">
                            Войти для бронирования
                        </button>
                        <p class="text-muted text-center small mb-0">Онлайн-резервирование доступно только авторизованным клиентам.</p>
                    @endauth
                @else
                    <button class="btn btn-secondary btn-lg w-100 py-3 fw-bold rounded-3 mb-3" disabled>
                        Автомобиль недоступен
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Блок подробных характеристик и комплектации -->
    <div class="row mt-5">
        <div class="col-12 col-lg-7">
            <!-- Таблица технических характеристик -->
            <div class="bg-white p-4 rounded-3 shadow-sm mb-4">
                <h3 class="fw-bold text-dark mb-4 h4 border-bottom pb-2">Детальные технические характеристики</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-borderless align-middle mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted py-2 w-50">Год выпуска</td>
                                <td class="fw-bold text-dark py-2">{{ $car->year }} г.</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2">Тип кузова</td>
                                <td class="fw-bold text-dark py-2">{{ $car->bodyType->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2">Объем двигателя</td>
                                <td class="fw-bold text-dark py-2">{{ number_format($car->engine_volume, 1) }} л.</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2">Коробка передач (КПП)</td>
                                <td class="fw-bold text-dark py-2">
                                    @if($car->transmission === 'manual') Механическая (МКПП)
                                    @elseif($car->transmission === 'automatic') Автоматическая (АКПП)
                                    @else Роботизированная @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2">Тип привода</td>
                                <td class="fw-bold text-dark py-2">
                                    @if($car->drive_type === 'FWD') Передний (FWD)
                                    @elseif($car->drive_type === 'RWD') Задний (RWD)
                                    @else Полный (AWD) @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2">Тип топлива</td>
                                <td class="fw-bold text-dark py-2">
                                    @if($car->fuel_type === 'petrol') Бензин
                                    @elseif($car->fuel_type === 'diesel') Дизель
                                    @elseif($car->fuel_type === 'electric') Электро
                                    @else Гибрид @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Блок Дополнительных опций (Многие-ко-многим `options`) -->
            <div class="bg-white p-4 rounded-3 shadow-sm">
                <h3 class="fw-bold text-dark mb-3 h4 border-bottom pb-2">Установленные дополнительные опции</h3>
                @if($car->options && $car->options->count() > 0)
                    <div class="row g-3 pt-2">
                        @foreach($car->options as $option)
                            <div class="col-12 col-md-6 d-flex align-items-center">
                                <div class="text-success me-2">✓</div>
                                <div>
                                    <span class="text-dark d-block fw-semibold">{{ $option->name }}</span>
                                    @if($option->price > 0)
                                        <small class="text-muted">+ {{ number_format($option->price, 0, ',', ' ') }} ₽</small>
                                    @else
                                        <small class="text-success">Включено в стоимость</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0 pt-2">Дополнительные опции для данного автомобиля не указаны. Комплектация базовая.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@auth
<div class="modal fade" id="orderReservationModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
@endauth
@endsection