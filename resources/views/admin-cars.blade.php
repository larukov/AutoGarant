@extends('layouts.app')

@section('title', 'Управление каталогом СРМ — Автосалон')

@section('content')
    <div class="container-fluid py-5 px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold h2 text-dark">Управление каталогом автомобилей</h1>
            <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary btn-sm fw-bold">← К заказам</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-4">{{ session('error') }}</div>
        @endif

        <div class="row g-4">
            <!-- Левая колонка: Форма добавления (Управление без вмешательства в код) -->
            <div class="col-12 col-xl-4">
                <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
                    <h3 class="fw-bold h5 text-dark mb-3 border-bottom pb-2">Добавить автомобиль в базу</h3>
                    <form action="{{ route('admin.cars.store') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted">Марка и модель</label>
                            <select name="model_id" class="form-select form-select-sm" required>
                                @foreach($models as $model)
                                    <option value="{{ $model->id }}">{{ $model->brand->name }} {{ $model->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted">Тип кузова</label>
                            <select name="body_type_id" class="form-select form-select-sm" required>
                                @foreach($bodyTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted">Год выпуска</label>
                                <input type="number" name="year" class="form-control form-control-sm"
                                    value="{{ date('Y') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted">Объем двигателя (л)</label>
                                <input type="number" name="engine_volume" step="0.1" class="form-control form-control-sm"
                                    placeholder="2.0" required>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted">Стоимость (₽)</label>
                            <input type="number" name="price" class="form-control form-control-sm"
                                placeholder="Цена в рублях" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted">Коробка передач</label>
                            <select name="transmission" class="form-select form-select-sm">
                                <option value="manual">Механическая (МКПП)</option>
                                <option value="automatic">Автоматическая (АКПП)</option>
                                <option value="robot">Роботизированная</option>
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted">Привод</label>
                                <select name="drive_type" class="form-select form-select-sm">
                                    <option value="FWD">Передний</option>
                                    <option value="RWD">Задний</option>
                                    <option value="AWD">Полный</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted">Топливо</label>
                                <select name="fuel_type" class="form-select form-select-sm">
                                    <option value="petrol">Бензин</option>
                                    <option value="diesel">Дизель</option>
                                    <option value="electric">Электро</option>
                                    <option value="hybrid">Гибрид</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="status" value="available">
                        <button type="submit" class="btn btn-success btn-sm w-100 fw-bold py-2">Сохранить в базу
                            данных</button>
                    </form>
                </div>
            </div>

            <!-- Правая колонка: Таблица позиций с возможностью редактирования и удаления -->
            <div class="col-12 col-xl-8">
                <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
                    <h3 class="fw-bold h5 text-dark mb-4 border-bottom pb-2">Текущие позиции на складе</h3>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-sm" style="font-size: 0.9rem;">
                            <thead class="table-light text-secondary small text-uppercase">
                                <tr>
                                    <th>Автомобиль</th>
                                    <th>Характеристики</th>
                                    <th>Изменить цену (₽)</th>
                                    <th>Статус / КПП</th>
                                    <th class="text-center">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cars as $car)
                                                <tr>
                                                    <form action="{{ route('admin.cars.update', $car->id) }}" method="POST">
                                                        @csrf
                                                        <!-- Автомобиль -->
                                                        <td>
                                                            <span class="fw-bold text-dark d-block">{{ $car->model->brand->name }}
                                                                {{ $car->model->name }}</span>
                                                            <small class="text-muted">{{ $car->bodyType->name }}, {{ $car->year }}
                                                                г.</small>
                                                        </td>
                                                        <!-- Технические характеристики -->
                                                        <td>
                                                            <small class="d-block">{{ number_format($car->engine_volume, 1) }} л /
                                                                {{ $car->drive_type }}</small>
                                                        </td>
                                                        <!-- Изменение цены -->
                                                        <td>
                                                            <input type="number" name="price"
                                                                class="form-control form-control-sm fw-bold text-primary"
                                                                value="{{ (int) $car->price }}" style="max-width: 130px;">
                                                        </td>
                                                        <!-- Изменение комплектации (КПП) и статуса наличия -->
                                                        <td>
                                                            <select name="status" class="form-select form-select-sm mb-1 px-1"
                                                                style="font-size: 0.8rem;">
                                                                <option value="available" {{ $car->status === 'available' ? 'selected' : '' }}>В наличии</option>
                                                                <option value="reserved" {{ $car->status === 'reserved' ? 'selected' : '' }}>
                                                                    Бронь</option>
                                                                <option value="sold" {{ $car->status === 'sold' ? 'selected' : '' }}>Продан
                                                                </option>
                                                            </select>
                                                            <select name="transmission" class="form-select form-select-sm px-1"
                                                                style="font-size: 0.8rem;">
                                                                <option value="manual" {{ $car->transmission === 'manual' ? 'selected' : '' }}>МКПП</option>
                                                                <option value="automatic" {{ $car->transmission === 'automatic' ? 'selected' : '' }}>АКПП</option>
                                                                <option value="robot" {{ $car->transmission === 'robot' ? 'selected' : '' }}>
                                                                    Робот</option>
                                                            </select>
                                                        </td>
                                                        <!-- Кнопки Сохранить / Удалить -->
                                                        <td class="text-center">
                                                            <div class="d-flex gap-1 justify-content-center">
                                                                <button type="submit" class="btn btn-primary btn-sm"
                                                                    title="Сохранить изменения">✓</button>
                                                    </form>
                                                    <!-- Отдельная форма под метод DELETE для удаления позиции -->
                                                    <form action="{{ route('admin.cars.destroy', $car->id) }}" method="POST"
                                                        onsubmit="return confirm('Вы уверены, что хотите безвозвратно удалить этот автомобиль из базы данных?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Удалить из БД">✕</button>
                                                    </form>
                                    </div>
                                    </td>
                                    </tr>
                                @endforeach
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection