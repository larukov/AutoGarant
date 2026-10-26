<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;

class HomeController extends Controller
{
     public function index()
    {
        $popularCars = Car::with(['model.brand', 'bodyType', 'images'])
            ->where('status', 'available')->orderBy('created_at', 'desc')->take(3)->get();
        return view('home', compact('popularCars'));
    }
}
