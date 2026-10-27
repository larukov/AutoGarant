<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CatalogController;
use Illuminate\Support\Facades\Auth;
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

Route::get("catalog/show/{id}", [CatalogController::class, "show"])->name("catalog.show");

Route::post('/', [RegisterController::class, "store"])->name("register");
Route::post('/login', [LoginController::class, 'store'])->name('login');
Route::get('/logout', function() {
    Auth::logout();
    return redirect('/');
})->name('logout');
// Route::get("/",[LoginController::class, "index"])->name("login");