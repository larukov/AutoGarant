@extends('layouts.app')

@section('title', 'Редактировать автомобиль — Автосалон')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold h2 text-dark mb-0">Редактирование автомобиля</h1>
            <a href="{{ route('admin.cars.index') }}" class="btn btn-outline-secondary fw-bold">← К списку машин</a>
        </div>

        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
            <form action="{{ route('admin.cars.update', $car->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                @include('admin.cars._form', ['car' => $car])
            </form>
        </div>

        @if($car->images->count() > 0)
            <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
                <h3 class="fw-bold h5 text-dark mb-3 border-bottom pb-2">Загруженные фотографии</h3>
                <div class="row g-2">
                    @foreach($car->images as $image)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="border rounded overflow-hidden position-relative">
                                <img src="{{ asset($image->image_url) }}" alt="{{ $image->name }}"
                                    style="width: 100%; height: 180px; object-fit: cover;">
                                <form action="{{ route('admin.cars.images.destroy', $image->id) }}" method="POST"
                                    onsubmit="return confirm('Удалить это изображение?');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="car_id" value="{{ $car->id }}">
                                    <button type="submit"
                                        class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                                        title="Удалить фото">✕</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
