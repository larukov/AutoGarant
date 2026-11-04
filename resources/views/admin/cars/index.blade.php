@extends('layouts.app')

@section('title', 'Управление каталогом — Автосалон')

@section('content')
    <div class="container-fluid py-5 px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold h2 text-dark">Управление каталогом автомобилей</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.cars.create') }}" class="btn btn-success fw-bold">+ Добавить автомобиль</a>
                <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary fw-bold">← К заказам</a>
            </div>
        </div>

        @if (session('success') || session('error'))
            <x-alert class="mb-4" />
        @endif

        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
            <h3 class="fw-bold h5 text-dark mb-4 border-bottom pb-2">Текущие позиции на складе</h3>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th>Автомобиль</th>
                            <th>Характеристики</th>
                            <th>Фото</th>
                            <th>Цена (₽)</th>
                            <th>Статус / КПП</th>
                            <th class="text-center">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cars as $car)
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark d-block">{{ $car->model->brand->name }}
                                        {{ $car->model->name }}</span>
                                    <small class="text-muted">{{ $car->bodyType->name }}, {{ $car->year }} г.</small>
                                </td>
                                <td>
                                    <small class="d-block">{{ number_format($car->engine_volume, 1, ',', ' ') }} л /
                                        {{ $car->drive_type }}</small>
                                    <small class="text-muted"><x-spec-text field="fuel_type" :value="$car->fuel_type" /></small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $car->images->count() }} фото</span>
                                </td>
                                <td class="fw-bold text-primary"><x-money :money="$car->price" /></td>
                                <td>
                                    <x-car-status :car="$car" compact />
                                    <small class="d-block text-muted mt-1">
                                        <x-spec-text field="transmission" :value="$car->transmission" short />
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="{{ route('admin.cars.edit', $car->id) }}" class="btn btn-primary btn-sm"
                                            title="Редактировать">✎</a>
                                        <form action="{{ route('admin.cars.destroy', $car->id) }}" method="POST"
                                            onsubmit="return confirm('Вы уверены, что хотите безвозвратно удалить этот автомобиль из базы данных?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                title="Удалить из БД">✕</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">В базе пока нет автомобилей.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
