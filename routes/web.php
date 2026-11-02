<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
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

Route::get("catalog", [CatalogController::class, "index"])->name("catalog.index");

Route::get("catalog/show/{id}", [CatalogController::class, "show"])->name("catalog.show");

Route::post('/', [RegisterController::class, "store"])->name("register");
Route::post('/login', [LoginController::class, 'store'])->name('login');


Route::middleware(['auth'])->group(function () {
    
    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/');
    })->name('logout');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/admin/order/{id}/update', [AdminController::class, 'updateStatus'])->name('admin.orders.update');

    Route::get('/admin/cars', [AdminController::class, 'carsIndex'])->name('admin.cars.index');
    Route::get('/admin/cars/create', [AdminController::class, 'carCreate'])->name('admin.cars.create');
    Route::post('/admin/cars/store', [AdminController::class, 'carStore'])->name('admin.cars.store');
    Route::get('/admin/cars/{id}/edit', [AdminController::class, 'carEdit'])->name('admin.cars.edit');
    Route::post('/admin/cars/{id}/update', [AdminController::class, 'carUpdate'])->name('admin.cars.update');
    Route::delete('/admin/cars/{id}/delete', [AdminController::class, 'carDestroy'])->name('admin.cars.destroy');
    Route::delete('/admin/cars/images/{id}/delete', [AdminController::class, 'imageDestroy'])->name('admin.cars.images.destroy');
});
