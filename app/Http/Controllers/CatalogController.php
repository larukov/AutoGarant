<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Brand;
use App\Models\BodyType;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        // Загрузка списков для фильтров
        $brands = Brand::orderBy('name')->get();
        $bodyTypes = BodyType::orderBy('name')->get();

        $query = Car::with(['model.brand', 'bodyType', 'images']);

        // Фильтрация
        if ($request->filled('brand_id')) {
            $query->whereHas('model', function ($q) use ($request) {
                $q->where('brand_id', $request->brand_id);
            });
        }
        if ($request->filled('body_type_id')) {
            $query->where('body_type_id', $request->body_type_id);
        }
        if ($request->filled('transmission')) {
            $query->where('transmission', $request->transmission);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        $cars = $query->orderBy('price', 'asc')->paginate(9)->withQueryString();

        return view('catalog.index', compact('cars', 'brands', 'bodyTypes'));
    }
     public function show($id)
    {
        $car = Car::with(['model.brand', 'bodyType', 'images', 'options'])->findOrFail($id);

        return view('catalog.show', compact('car'));
    }
}
