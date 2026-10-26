@extends('layouts.app')

@section('title', 'Главная — Автосалон в Каменске-Уральском')

@section('content')

    <!-- 1. ИНТЕРАКТИВНЫЙ СЛАЙДЕР С ПОПУЛЯРНЫМИ МОДЕЛЯМИ И АКЦИЯМИ (Carousel) -->
    <section id="heroSlider" class="carousel slide carousel-fade bg-dark" data-bs-ride="carousel" data-bs-interval="5000">
        <!-- Индикаторы (полоски снизу) -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="0" class="active" aria-current="true"
                aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="1" aria-label="Slide 2"></button>
        </div>

        <div class="carousel-inner">
            <!-- Слайд 1 -->
            <div class="carousel-item active"
                style="min-height: 550px; background: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)), #1e293b url('/images/hero-bg.jpg') center/cover no-repeat;">
                <div class="container h-100 d-flex align-items-center" style="min-height: 550px;">
                    <div class="col-lg-7 text-white py-5">
                        <span class="badge bg-primary text-uppercase mb-3 px-3 py-2 rounded-pill">
                            Спецпредложение по автокредиту
                        </span>
                        <h1 class="display-4 fw-black mb-3">
                            Найдите свой <br><span class="text-primary">идеальный автомобиль</span>
                        </h1>
                        <p class="lead text-muted-light mb-4">
                            Удобный параметрический поиск, честное онлайн-бронирование со склада и расчет кредитной ставки
                            от 4.9% годовых в автосалоне.
                        </p>
                        <div class="d-grid gap-2 d-md-flex">
                            <a href="{{ route('catalog.index') }}"
                                class="btn btn-primary btn-lg px-4 me-md-2 fw-bold shadow-sm">
                                Перейти в каталог
                            </a>
                            <a href="#advantages" class="btn btn-outline-light btn-lg px-4">
                                Узнать больше
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Слайд 2 -->
            <div class="carousel-item" style="min-height: 550px; background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), #0f172a url('/images/slider-bg-2.jpg') center/cover no-repeat;">
                <div class="container h-100 d-flex align-items-center" style="min-height: 550px;">
                    <div class="col-lg-7 text-white py-5">
                        <span class="badge bg-danger text-uppercase mb-3 px-3 py-2 rounded-pill">
                            Новое поступление
                        </span>
                        <h1 class="display-4 fw-black mb-3">
                            Автомобили <br><span class="text-danger">в наличии</span>
                        </h1>
                        <p class="lead text-muted-light mb-4">
                            Большой выбор комплектаций, прозрачные статусы сделок и моментальное онлайн-резервирование со склада автосалона.
                        </p>
                        <div class="d-grid gap-2 d-md-flex">
                            <a href="{{ route('catalog.index') }}" class="btn btn-danger btn-lg px-4 me-md-2 fw-bold shadow-sm">
                                Открыть каталог
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Кнопки управления (Стрелки) с исправленными атрибутами -->
        <button class="carousel-control-prev d-none d-md-flex" type="button" data-bs-target="#heroSlider"
            data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Назад</span>
        </button>
        <button class="carousel-control-next d-none d-md-flex" type="button" data-bs-target="#heroSlider"
            data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Вперед</span>
        </button>
    </section>

    <!-- 2. ПОПУЛЯРНЫЕ МОДЕЛИ В НАЛИЧИИ (Вывод на основе ER-модели) -->
    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 pb-2">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Популярные предложения</h2>
                    <p class="text-muted mb-0">Свежие поступления автомобилей на нашем складе</p>
                </div>
                <a href="{{ route('catalog.index') }}"
                    class="text-primary fw-semibold text-decoration-none mt-2 mt-md-0 d-inline-flex align-items-center gap-1">
                    Смотреть все автомобили &rarr;
                </a>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                @foreach($popularCars as $car)
                    <div class="col d-flex">
                        <div
                            class="card w-100 shadow-sm border-0 rounded-3 overflow-hidden h-100 d-flex flex-column justify-content-between">
                            <!-- Изображение из связанной таблицы images -->
                            <div class="position-relative bg-secondary" style="height: 220px;">
                                <img src="{{ $car->images->first()->image_url ?? '/images/default-car.jpg' }}"
                                    alt="{{ $car->model->brand->name }} {{ $car->model->name }}"
                                    class="w-100 h-100 object-cover">

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
                @endforeach
            </div>
        </div>
    </section>

    <!-- 3. ИНФОРМАЦИОННЫЙ БЛОК С КЛЮЧЕВЫМИ ПРЕИМУЩЕСТВАМИ -->
    <section id="advantages" class="py-5 bg-white">
        <div class="container py-4">
            <div class="text-center mx-auto mb-5" style="max-width: 700px;">
                <h2 class="fw-bold text-dark">Преимущества нашего автосалона</h2>
                <p class="text-muted">Прозрачные условия и полная автоматизация процессов покупки автомобиля</p>
            </div>
            
            <div class="row g-4 mt-2 justify-content-center">
                <!-- Преимущество 1: Гарантия -->
                <div class="col-md-5 text-center">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded-3 mb-3 p-3 shadow-sm" style="width: 60px; height: 60px;">
                        <svg xmlns="http://w3.org" width="28" height="28" fill="currentColor" class="bi bi-shield-check" viewBox="0 0 16 16">
                            <path d="M5.338 1.59a61.447 61.447 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233.466.466 0 0 0 .58 0a10.725 10.725 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.775 11.775 0 0 1-2.517 2.453 1.935 1.935 0 0 1-2.414 0 11.775 11.775 0 0 1-2.517-2.453C1.328 9.617.026 6.3-.57 1.822A1.54 1.54 0 0 1 .475 1.43c.658-.214 1.777-.57 2.887-.87z"/>
                            <path d="M10.854 5.854a.5.5 0 0 0-.708-.708L7.5 7.793 6.354 6.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z"/>
                        </svg>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Официальная гарантия</h5>
                    <p class="text-muted small px-lg-4">
                        Полная техническая и юридическая защита. До 5 лет полноценного обслуживания от автосалона на основные узлы и агрегаты.
                    </p>
                </div>

                <!-- Преимущество 2: Автокредит -->
                <div class="col-md-5 text-center">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded-3 mb-3 p-3 shadow-sm" style="width: 60px; height: 60px;">
                        <svg xmlns="http://w3.org" width="28" height="28" fill="currentColor" class="bi bi-calculator" viewBox="0 0 16 16">
                            <path d="M12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h8zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4z"/>
                            <path d="M4 2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h7a.5.5 0 0 1-.5-.5v-2zm1 4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm-6 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm-6 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z"/>
                        </svg>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Онлайн-расчет кредита</h5>
                    <p class="text-muted small px-lg-4">
                        Интерактивный калькулятор на сайте. Рассчитайте платежи в модуле `credit_applications` и отправьте анкету за минуту.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection