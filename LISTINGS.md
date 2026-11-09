# Листинги для пояснительной записки

Проект: **AutoGarant** — информационная система автосалона (Laravel 10, MySQL, Bootstrap 5).

## Содержание

1. [Структура базы данных (миграции)](#1-структура-базы-данных-миграции)
2. [Модели Eloquent](#2-модели-eloquent)
3. [Маршруты приложения](#3-маршруты-приложения)
4. [Контроллеры](#4-контроллеры)
5. [Сидеры](#5-сидеры)
6. [Blade-компоненты](#6-blade-компоненты)

---

## 1. Структура базы данных (миграции)

### Листинг 1. Миграция таблицы `users`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('tel')->unique();
            $table->boolean('is_admin')->default(false);
            $table->boolean('status')->default(true);
            $table->rememberToken();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

### Листинг 2. Миграция таблицы `cars`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('model_id')->constrained('models');
            $table->year('year');
            $table->decimal('price', 12, 2);
            $table->foreignId('body_type_id')->constrained('body_type');
            $table->decimal('engine_volume', 3, 1);
            $table->enum('transmission', ['manual', 'automatic', 'robot']);
            $table->enum('drive_type', ['FWD', 'RWD', 'AWD']);
            $table->enum('fuel_type', ['petrol', 'diesel', 'electric', 'hybrid']);
            $table->enum('status', ['available', 'reserved', 'sold'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
```

### Листинг 3. Миграция таблицы `orders`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('car_id')->constrained('cars');
            $table->enum('payment_type', ['cash', 'credit', 'card']);
            $table->enum('payment_status', ['unpaid', '10%', '50%', 'fully_paid'])->default('unpaid');
            $table->decimal('final_price', 12, 2);
            $table->enum('status', ['created', 'confirmed', 'rejected'])->default('created');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
```

### Листинг 4. Миграция таблицы `credit_applications`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->decimal('down_payment', 12, 2);
            $table->integer('loan_term_months');
            $table->decimal('interest_rate', 5, 2);
            $table->decimal('monthly_payment', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_applications');
    }
};
```

### Листинг 5. Миграции вспомогательных таблиц (`brands`, `models`, `body_type`, `options`, `images`, `car_option`)

```php
<?php

// brands
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
```

```php
<?php

// models
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('models');
    }
};
```

```php
<?php

// body_type
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('body_type', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('body_type');
    }
};
```

```php
<?php

// options
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('price');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('options');
    }
};
```

```php
<?php

// images
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('car_id')->constrained('cars')->cascadeOnDelete();
            $table->string('image_url', 2048);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
```

```php
<?php

// car_option (сводная таблица «многие-ко-многим» автомобилей и опций)
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_option', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained('cars')->onDelete('cascade');
            $table->foreignId('options_id')->constrained('options')->onDelete('cascade');
            $table->integer('count')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_option');
    }
};
```

---

## 2. Модели Eloquent

### Листинг 6. Модель `Car`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Car extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'model_id', 'year', 'price', 'body_type_id',
        'engine_volume', 'transmission', 'drive_type',
        'fuel_type', 'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'engine_volume' => 'decimal:1',
    ];

    public function model(): BelongsTo
    {
        return $this->belongsTo(CarModel::class, 'model_id');
    }

    public function bodyType(): BelongsTo
    {
        return $this->belongsTo(BodyType::class, 'body_type_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'car_id');
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(Option::class, 'car_option', 'car_id', 'options_id')
                ->withPivot('count');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'car_id');
    }
}
```

### Листинг 7. Модель `Order`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'car_id', 'payment_type',
        'payment_status', 'final_price', 'status'
    ];

    protected $casts = [
        'final_price' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function creditApplication(): HasOne
    {
        return $this->hasOne(CreditApplication::class, 'order_id');
    }
}
```

### Листинг 8. Модель `User`

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use Notifiable;

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'name', 'email', 'password', 'tel', 'is_admin', 'status'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'status' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }
}
```

### Листинг 9. Модели справочников (`Brand`, `CarModel`, `BodyType`, `Option`, `Image`, `CreditApplication`)

```php
<?php

// Brand
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];

    public function models(): HasMany
    {
        return $this->hasMany(CarModel::class, 'brand_id');
    }
}
```

```php
<?php

// CarModel
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarModel extends Model
{
    protected $table = 'models';
    public $timestamps = false;
    protected $fillable = ['brand_id', 'name'];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class, 'model_id');
    }
}
```

```php
<?php

// BodyType
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BodyType extends Model
{
    protected $table = 'body_type';
    public $timestamps = false;
    protected $fillable = ['name'];

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class, 'body_type_id');
    }
}
```

```php
<?php

// Option
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Option extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'price'];

    public function cars(): BelongsToMany
    {
        return $this->belongsToMany(Car::class, 'car_option', 'options_id', 'car_id')
                ->withPivot('count');
    }
}
```

```php
<?php

// Image
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'car_id', 'image_url'];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}
```

```php
<?php

// CreditApplication
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditApplication extends Model
{
    protected $table = 'credit_applications';
    public $timestamps = false;

    protected $fillable = [
        'order_id', 'down_payment', 'loan_term_months',
        'interest_rate', 'monthly_payment'
    ];

    protected $casts = [
        'down_payment' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
```

---

## 3. Маршруты приложения

### Листинг 10. Файл `routes/web.php`

```php
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

// Публичные маршруты
Route::get('/', [HomeController::class, 'index']);

Route::get('catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('catalog/show/{id}', [CatalogController::class, 'show'])->name('catalog.show');

Route::post('/', [RegisterController::class, 'store'])->name('register');
Route::post('/login', [LoginController::class, 'store'])->name('login');

// Маршруты, доступные только авторизованным пользователям
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
```

---

## 4. Контроллеры

### Листинг 11. Контроллер `CatalogController`

```php
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
```

### Листинг 12. Контроллер `OrderController`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Order;
use App\Models\CreditApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // 1. Валидация входящих данных из формы модального окна
        $request->validate([
            'car_id' => 'required|exists:cars,id',
            'payment_type' => 'required|in:cash,card,credit',
            'options' => 'sometimes|array',
            'options.*' => 'exists:options,id',
        ]);

        if ($request->payment_type === 'credit') {
            $request->validate([
                'down_payment' => 'required|numeric|min:0',
                'loan_term_months' => 'required|integer|min:1|max:120',
                'interest_rate' => 'required|numeric|min:0|max:100',
            ]);
        }

        // 2. Находим автомобиль по ID
        $car = Car::with('options')->findOrFail($request->car_id);

        // 3. Проверяем бизнес-ограничение: доступна ли машина для заказа
        if ($car->status !== 'available') {
            return redirect()->back()->with('error', 'К сожалению, этот автомобиль уже забронирован или продан.');
        }

        // 3.1. Выбранные пользователем дополнительные опции
        $optionIds = $request->input('options', []);
        $selectedOptions = $car->options->whereIn('id', $optionIds);

        // 4. Используем транзакцию базы данных, чтобы гарантировать целостность данных
        DB::beginTransaction();

        // Создаем запись заказа в таблице orders
        $order = new Order();
        $order->user_id = Auth::id(); // ID текущего авторизованного клиента
        $order->car_id = $car->id;
        $order->payment_type = $request->payment_type;
        $order->payment_status = 'unpaid'; // По умолчанию неоплачено
        $order->final_price = $car->price + $selectedOptions->sum('price');
        $order->status = 'created'; // Начальный статус сделки
        $order->save();

        // Меняем статус самого автомобиля на «Забронирован»
        $car->status = 'reserved';
        $car->save();

        // 5. Если выбран тип оплаты «Кредит», создаем связанную запись (1:1)
        if ($request->payment_type === 'credit') {
            $downPayment = $request->input('down_payment', 0);
            $loanTermMonths = $request->input('loan_term_months', 36);
            $interestRate = $request->input('interest_rate', 11.5);

            $loanAmount = $order->final_price - $downPayment;
            $monthlyPayment = 0;

            if ($loanAmount > 0 && $loanTermMonths > 0) {
                $monthlyRate = $interestRate / 12 / 100;
                $coefficient = ($monthlyRate * pow(1 + $monthlyRate, $loanTermMonths))
                    / (pow(1 + $monthlyRate, $loanTermMonths) - 1);
                $monthlyPayment = round($loanAmount * $coefficient, 2);
            }

            $credit = new CreditApplication();
            $credit->order_id = $order->id;
            $credit->down_payment = $downPayment;
            $credit->loan_term_months = $loanTermMonths;
            $credit->interest_rate = $interestRate;
            $credit->monthly_payment = $monthlyPayment;
            $credit->save();
        }

        // Фиксируем изменения в БД
        DB::commit();

        return redirect()->route('profile.index')
            ->with('success', 'Автомобиль успешно зарезервирован! Менеджер свяжется с вами в ближайшее время. Отслеживать статус можно в личном кабинете.');
    }
}
```

### Листинг 13. Контроллер `AdminController`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\Order;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\BodyType;
use App\Models\Brand;
use App\Models\Option;
use App\Models\Image;

class AdminController extends Controller
{
    public function index()
    {
        // Вывод списка всех заявок
        $orders = Order::with(['car.model.brand', 'user', 'creditApplication'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Формирование финансового отчета по закрытым сделкам
        $totalSales = Order::where('status', 'confirmed')->sum('final_price');
        $closedDealsCount = Order::where('status', 'confirmed')->count();

        return view('admin.index', compact('orders', 'totalSales', 'closedDealsCount'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:created,confirmed,rejected',
            'payment_status' => 'required|in:unpaid,10%,50%,fully_paid',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->payment_status = $request->payment_status;
        $order->save();

        // Если сделка отклонена менеджером, возвращаем авто в статус «в наличии»
        if ($request->status === 'rejected') {
            $car = Car::find($order->car_id);
            if ($car) {
                $car->status = 'available';
                $car->save();
            }
        }

        return redirect()->back()->with('success', 'Статус сделки успешно обновлен!');
    }

    protected function carValidate(Request $request)
    {
        $data = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'model_id' => 'required|exists:models,id',
            'body_type_id' => 'required|exists:body_type,id',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'price' => 'required|numeric|min:0',
            'engine_volume' => 'required|numeric|min:0.1|max:99.9',
            'transmission' => 'required|in:manual,automatic,robot',
            'drive_type' => 'required|in:FWD,RWD,AWD',
            'fuel_type' => 'required|in:petrol,diesel,electric,hybrid',
            'status' => 'required|in:available,reserved,sold',
            'options' => 'sometimes|array',
            'options.*' => 'exists:options,id',
            'images' => 'sometimes|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'engine_volume.min' => 'Объём двигателя должен быть не меньше 0.1 л.',
            'engine_volume.max' => 'Объём двигателя не может превышать 99.9 л.',
        ]);

        // Проверяем, что выбранная модель действительно относится к выбранной марке
        $model = CarModel::findOrFail($data['model_id']);
        if ((int) $model->brand_id !== (int) $data['brand_id']) {
            throw ValidationException::withMessages([
                'model_id' => 'Выбранная модель не соответствует указанной марке.',
            ]);
        }

        return $data;
    }

    protected function handleImages(Request $request, Car $car)
    {
        if (!$request->hasFile('images')) {
            return;
        }

        foreach ($request->file('images') as $file) {
            $name = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/images/cars', $name);
            $imageUrl = Storage::url('images/cars/' . $name);

            $car->images()->create([
                'name' => $file->getClientOriginalName(),
                'image_url' => $imageUrl,
            ]);
        }
    }

    public function carStore(Request $request)
    {
        $data = $this->carValidate($request);

        $car = Car::create($data);

        $this->handleOptionSelection($car, $data['options'] ?? []);
        $this->handleImages($request, $car);

        return redirect()->route('admin.cars.index')->with('success', 'Новый автомобиль успешно добавлен в каталог!');
    }

    public function carDestroy($id)
    {
        $car = Car::findOrFail($id);

        // Перед удалением проверяем, нет ли активных заказов на этот авто
        if (Order::where('car_id', $id)->where('status', '!=', 'rejected')->exists()) {
            return redirect()->back()->with('error', 'Нельзя удалить автомобиль, на который оформлен активный заказ!');
        }

        $car->delete();
        return redirect()->back()->with('success', 'Автомобиль успешно удален из базы данных.');
    }
}
```

---

## 5. Сидеры

### Листинг 14. `BrandAndModelSeeder`

```php
<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandAndModelSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Lada' => ['Vesta', 'Granta', 'Niva Travel'],
            'Haval' => ['Jolion', 'F7', 'Dargo'],
            'Chery' => ['Tiggo 4 Pro', 'Tiggo 7 Pro'],
            'Geely' => ['Monjaro', 'Coolray'],
        ];

        foreach ($data as $brandName => $models) {
            $brand = Brand::create(['name' => $brandName]);

            foreach ($models as $modelName) {
                $brand->models()->create(['name' => $modelName]);
            }
        }
    }
}
```

### Листинг 15. `CarSeeder`

```php
<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarModel;
use App\Models\BodyType;
use App\Models\Option;
use App\Models\User;
use App\Models\Order;
use App\Models\CreditApplication;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    public function run(): void
    {
        $models = CarModel::all();
        $bodyTypes = BodyType::all();
        $options = Option::all();
        $client = User::where('is_admin', false)->first();

        $transmissions = ['manual', 'automatic', 'robot'];
        $driveTypes = ['FWD', 'RWD', 'AWD'];
        $fuelTypes = ['petrol', 'diesel', 'electric', 'hybrid'];
        $statuses = ['available', 'available', 'available', 'reserved', 'sold'];

        // Генерируем 11 автомобилей
        for ($i = 1; $i <= 11; $i++) {
            $price = rand(1500000, 4500000);

            $car = Car::create([
                'model_id' => $models->random()->id,
                'body_type_id' => $bodyTypes->random()->id,
                'year' => rand(2023, 2026),
                'price' => $price,
                'engine_volume' => collect([1.5, 1.6, 2.0, 2.4])->random(),
                'transmission' => collect($transmissions)->random(),
                'drive_type' => collect($driveTypes)->random(),
                'fuel_type' => collect($fuelTypes)->random(),
                'status' => collect($statuses)->random(),
            ]);

            // Опции (связь Многие-ко-многим). Изображения назначает ImageSeeder
            $carOptions = $options->random(rand(1, 2));
            foreach ($carOptions as $option) {
                $car->options()->attach($option->id, ['count' => 1]);
            }

            // Для демонстрации: если машина зарезервирована или продана — создаем заказ
            if (in_array($car->status, ['reserved', 'sold']) && $client) {
                $paymentStatus = $car->status === 'sold' ? 'fully_paid' : '50%';
                $orderStatus = $car->status === 'sold' ? 'confirmed' : 'created';

                $order = Order::create([
                    'user_id' => $client->id,
                    'car_id' => $car->id,
                    'payment_type' => collect(['cash', 'credit', 'card'])->random(),
                    'payment_status' => $paymentStatus,
                    'final_price' => $car->price + $carOptions->sum('price'),
                    'status' => $orderStatus,
                ]);

                // Если тип оплаты кредитный — создаем связанную кредитную заявку (1:1)
                if ($order->payment_type === 'credit') {
                    $downPayment = $order->final_price * 0.20; // 20% взнос
                    CreditApplication::create([
                        'order_id' => $order->id,
                        'down_payment' => $downPayment,
                        'loan_term_months' => collect([36, 48, 60])->random(),
                        'interest_rate' => 14.90,
                        'monthly_payment' => ($order->final_price - $downPayment) / 48 * 1.1,
                    ]);
                }
            }
        }
    }
}
```

### Листинг 16. `ImageSeeder`

```php
<?php

namespace Database\Seeders;

use App\Models\Car;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    // Соответствие модели файлам картинок в storage/app/public/images/cars/
    protected array $modelImages = [
        'Lada Vesta' => [7],
        'Lada Granta' => [2],
        'Lada Niva Travel' => [8],
        'Haval Jolion' => [9],
        'Haval F7' => [5],
        'Haval Dargo' => [5],
        'Chery Tiggo 4 Pro' => [6, 11],
        'Chery Tiggo 7 Pro' => [4, 10],
        'Geely Monjaro' => [3],
        'Geely Coolray' => [1],
    ];

    public function run(): void
    {
        $cars = Car::with('model.brand')->get();

        foreach ($cars as $car) {
            // Удаляем старые записи, чтобы не задваивать
            $car->images()->delete();

            $fileNumbers = $this->imagesFor($car);

            foreach ($fileNumbers as $imageNumber) {
                $car->images()->create([
                    'name' => $this->filename($imageNumber, $car),
                    'image_url' => "/storage/images/cars/{$imageNumber}.jpg",
                ]);
            }
        }
    }

    protected function imagesFor(Car $car): array
    {
        $brand = optional($car->model->brand ?? null)->name;
        $model = optional($car->model)->name;
        $key = trim("{$brand} {$model}");

        if (isset($this->modelImages[$key])) {
            return $this->modelImages[$key];
        }

        if ($brand !== null) {
            foreach ($this->modelImages as $candidate => $files) {
                if (str_starts_with($candidate, $brand . ' ')) {
                    return $files;
                }
            }
        }

        return [1];
    }

    protected function filename(int $number, Car $car): string
    {
        $model = optional($car->model)->name ?? "Автомобиль #{$car->id}";
        $brand = optional($car->model->brand ?? null)->name;

        return trim("{$brand} {$model}") . " — фото №{$number}";
    }
}
```

### Листинг 17. `UserSeeder`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Ирина',
                'email' => 'admin@autogarant.ru',
                'password' => Hash::make('admin123'),
                'tel' => '+79991112233',
                'is_admin' => true,
                'status' => true,
            ]
        ];

        foreach ($users as $data) {
            User::updateOrCreate(['email' => $data['email']], $data);
        }
    }
}
```

### Листинг 18. `DatabaseSeeder`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BrandAndModelSeeder::class,
            BodyTypeSeeder::class,
            OptionSeeder::class,
            CarSeeder::class,
            ImageSeeder::class,
        ]);
    }
}
```

---

## 6. Blade-компоненты

### Листинг 19. UI-компонент `checkbox`

```blade
{{-- resources/views/components/ui/checkbox.blade.php --}}
@props([
    'name',
    'value' => null,
    'label' => null,
    'checked' => false,
    'id' => null,
    'class' => '',
    'labelClass' => '',
    'disabled' => false,
])

@php
    $checkboxId = $id ?? 'checkbox-' . $name . '-' . uniqid();
@endphp

<input type="checkbox" name="{{ $name }}" id="{{ $checkboxId }}" value="{{ $value }}"
    class="form-check-input @error($name) is-invalid @enderror {{ $class }}"
    @if ($checked) checked @endif
    @if ($disabled) disabled @endif
    {{ $attributes->except(['class', 'label', 'labelClass', 'value', 'checked']) }}>

@if ($label)
    <label class="form-check-label {{ $labelClass }}" for="{{ $checkboxId }}">
        {{ $label }}
    </label>
@endif
```

### Листинг 20. UI-компонент `input`

```blade
{{-- resources/views/components/ui/input.blade.php --}}
@props([
    'name',
    'type' => 'text',
    'label' => null,
    'placeholder' => '',
    'value' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'autocomplete' => null,
    'id' => null,
])

@php
    $inputId = $id ?? 'input-' . $name . '-' . uniqid();
    $hasError = $errors->has($name);
    $oldValue = old($name, $value ?? '');
@endphp

<div class="mb-3">
    @if ($label)
        <label for="{{ $inputId }}" class="form-label">
            {{ $label }}
            @if ($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <input type="{{ $type }}" name="{{ $name }}" id="{{ $inputId }}"
        class="form-control @error($name) is-invalid @enderror {{ $attributes->get('class', '') }}"
        placeholder="{{ $placeholder }}" value="{{ $oldValue }}" @if ($required) required @endif
        @if ($disabled) disabled @endif @if ($readonly) readonly @endif
        @if ($autocomplete) autocomplete="{{ $autocomplete }}" @else
        autocomplete="off" @endif
        {{ $attributes->except(['class']) }}>

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
```

---

## Примечания

- Листинги составлены по итоговому состоянию проекта, включая правки: идемпотентный `UserSeeder`, вынесенный `ImageSeeder` с привязкой фото к конкретным моделям, UI-компонент `checkbox`.
- Пути к файлам в листингах указаны относительно корня проекта.
- Для запуска сидеров используется команда `php artisan migrate:fresh --seed` (создаёт БД заново и наполняет демо-данными).