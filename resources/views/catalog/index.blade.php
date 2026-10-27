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