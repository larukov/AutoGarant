@extends('layouts.app')

@section('title', 'Редактировать автомобиль — Автосалон')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold h2 text-dark mb-0">Редактирование автомобиля</h1>
            <a href="{{ route('admin.cars.index') }}" class="btn btn-outline-secondary fw-bold">← К списку машин</a>
        </div>

        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
            <form action="{{ route('admin.cars.update', $car->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                @include('admin.cars._form', ['car' => $car])
            </form>
        </div>
    </div>
@endsection