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
                            <x-ui.select name="brand_id" label="Марка автомобиля" id="brand"
                                :options="$brands->pluck('name', 'id')" :value="request('brand_id')"
                                placeholder="Все марки" labelClass="form-label small fw-bold text-secondary"
                                class="form-select-sm" />
                        </div>

                        <!-- Фильтр по Типу кузова -->
                        <div class="mb-3">
                            <x-ui.select name="body_type_id" label="Тип кузова" id="body_type"
                                :options="$bodyTypes->pluck('name', 'id')"
                                :value="request('body_type_id')" placeholder="Все типы"
                                labelClass="form-label small fw-bold text-secondary" class="form-select-sm" />
                        </div>

                        <div class="mb-3">
                            <x-ui.select name="transmission" label="Коробка передач" id="transmission"
                                :options="[
                                    'manual' => 'Механика',
                                    'automatic' => 'Автомат',
                                    'robot' => 'Робот',
                                ]" :value="request('transmission')" placeholder="Любая КПП"
                                labelClass="form-label small fw-bold text-secondary" class="form-select-sm" />
                        </div>

                        <!-- Фильтр по Году выпуска -->
                        <div class="mb-3">
                            <x-ui.select name="year" label="Год выпуска" id="year"
                                :options="collect(range(2026, 2020))->mapWithKeys(fn ($y) => [$y => $y . ' г.'])->all()"
                                :value="request('year')" placeholder="Любой год"
                                labelClass="form-label small fw-bold text-secondary" class="form-select-sm" />
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
                            <x-card :car="$car" />
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
                <div class="mt-3">
                    {{ $cars->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>
@endsection