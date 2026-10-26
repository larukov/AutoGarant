<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CatalogController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, "index"]);

Route::get("catalog", [CatalogController::class,"index"])->name("catalog.index");

Route::get("catalog/show", function () {
    return view('home');
})->name("catalog.show");

// Route::get("/",[LoginController::class, "index"])->name("login");