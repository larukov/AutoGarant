# Программная реализация ИС «AutoGarant»

Информационная система автомобильного салона на Laravel (PHP), MySQL, Bootstrap 5.
Ниже по каждому реализованному модулю указаны путь к файлу, код и пояснение.

---

## Оглавление

1. [Маршруты приложения](#1-маршруты-приложения)
2. [Аутентификация (регистрация и вход)](#2-аутентификация)
3. [Главная страница](#3-главная-страница)
4. [Каталог автомобилей и фильтрация](#4-каталог-автомобилей)
5. [Карточка автомобиля](#5-карточка-автомобиля)
6. [Оформление заказа и расчёт кредита](#6-оформление-заказа)
7. [Личный кабинет клиента](#7-личный-кабинет-клиента)
8. [Администрирование: заявки и отчётность](#8-администрирование-заявок)
9. [Администрирование: управление автомобилями](#9-администрирование-автомобилей)
10. [Наполнение базы данных демо-данными (сидеры)](#10-сидеры)
11. [UI-компоненты Blade](#11-ui-компоненты-blade)

---

## 1. Маршруты приложения

**Реализовано:** все точки входа приложения — публичные и защищённые авторизацией.

**Путь:** `routes/web.php`

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

**Описание.** Маршруты приложения разделены на две группы. Публичные (`/`, `/catalog`, `/login`, `/register`) доступны всем посетителям сайта. Защищённые маршруты обёрнуты в middleware `auth` — они доступны только авторизованным пользователям: личный кабинет, оформление заказа и весь административный блок. Каждому маршруту присвоено именованное имя (например, `catalog.index`, `orders.store`), по которому на него ссылаются из представлений через функцию `route()`.

---

## 2. Аутентификация

**Реализовано:** регистрация пользователя и вход в систему с сохранением сессии.

**Путь:** `app/Http/Controllers/RegisterController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'email'      => 'required|email|unique:users,email',
            'name'       => 'required|string|max:255',
            'password'   => 'required|min:6|confirmed',
            'tel'        => 'required|unique:users,tel',
        ], [
            'tel.unique'           => 'Этот номер уже зарегистрирован',
            'email.unique'         => 'Этот email уже зарегистрирован',
            'password.confirmed'   => 'Пароли не совпадают',
            'password.min'         => 'Пароль должен содержать минимум 6 символов',
        ]);

        $validateData['password'] = Hash::make($validateData['password']);

        $user = User::create($validateData);

        Auth::login($user);

        return redirect()->back()->with('toast_success', 'Добро пожаловать, ' . $user->name . '! Регистрация прошла успешно.');
    }
}
```

**Описание.** Контроллер выполняет регистрацию нового пользователя. Сначала проверяются правила валидации: корректность email, уникальность email и телефона, длина пароля (минимум 6 символов) и совпадение подтверждающего пароля. Пароль перед сохранением хэшируется функцией `Hash::make()`. После создания записи в таблице `users` пользователь автоматически входит в систему через `Auth::login()`.

**Путь:** `app/Http/Controllers/LoginController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->back()->with('toast_success', 'С возвращением, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Неверный email или пароль.',
        ], 'login');
    }
}
```

**Описание.** Метод `store` обрабатывает отправку формы входа. Переданные email и пароль проверяются через `Auth::attempt()`. При успехе сессия пересоздаётся (`session()->regenerate()`) для защиты от фиксации сессии, и пользователь перенаправляется обратно с приветствием. При неудаче возвращается ошибка «Неверный email или пароль». Флаг `remember` реализует «запоминание» пользователя.

---

## 3. Главная страница

**Реализовано:** вывод трёх последних доступных автомобилей на главной странице.

**Путь:** `app/Http/Controllers/HomeController.php`

```php
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
```

**Описание.** Контроллер формирует блок «Популярные предложения»: выбираются автомобили со статусом `available` (в наличии), отсортированные по дате создания по убыванию, ограниченные тремя записями. Отношения `model.brand` (марка и модель), `bodyType` (тип кузова) и `images` (фотографии) подгружаются заранее через `with()`, что исключает лишние запросы к базе (N+1).

---

## 4. Каталог автомобилей

**Реализовано:** отображение каталога с фильтрацией по марке, кузову, КПП, году и максимальной цене, пагинация по 9 автомобилей на страницу.

**Путь:** `app/Http/Controllers/CatalogController.php`

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
}
```

**Описание.** Метод `index` выводит каталог. В начале загружаются справочники марок и типов кузова для панели фильтров. Затем строится запрос к автомобилям с подгрузкой зависимых сущностей. К запросу последовательно применяются условия фильтрации, заданные через GET-параметры: фильтр по марке выполняется через `whereHas` на связанную модель, остальные — напрямую по полям. Результат сортируется по цене и разбивается на страницы по 9 записей. `withQueryString()` сохраняет параметры фильтров при переходе между страницами пагинации.

---

## 5. Карточка автомобиля

**Реализовано:** детальная страница автомобиля с галереей изображений и списком доступных опций.

**Путь:** `app/Http/Controllers/CatalogController.php` (метод `show`)

```php
public function show($id)
{
    $car = Car::with(['model.brand', 'bodyType', 'images', 'options'])->findOrFail($id);

    return view('catalog.show', compact('car'));
}
```

**Описание.** Метод `show` находит автомобиль по идентификатору с загрузкой связей: марка и модель, тип кузова, галерея изображений и набор дополнительных опций (связь «многие-ко-многим»). `findOrFail` автоматически возвращает ошибку 404 при отсутствии автомобиля. Полученные данные передаются в представление `catalog.show` для построения полной карточки.

---

## 6. Оформление заказа

**Реализовано:** оформление заказа с расчётом итоговой цены, бронированием автомобиля и расчётом кредита.

**Путь:** `app/Http/Controllers/OrderController.php`

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

        // Дополнительная валидация при выборе кредита
        if ($request->payment_type === 'credit') {
            $request->validate([
                'down_payment' => 'required|numeric|min:0',
                'loan_term_months' => 'required|integer|min:1|max:120',
                'interest_rate' => 'required|numeric|min:0|max:100',
            ]);
        }

        $car = Car::with('options')->findOrFail($request->car_id);

        // Бизнес-ограничение: недоступная машина не заказывается
        if ($car->status !== 'available') {
            return redirect()->back()->with('error', 'К сожалению, этот автомобиль уже забронирован или продан.');
        }

        $optionIds = $request->input('options', []);
        $selectedOptions = $car->options->whereIn('id', $optionIds);

        // Транзакция для гарантии целостности данных заказа
        DB::beginTransaction();

        $order = new Order();
        $order->user_id = Auth::id();
        $order->car_id = $car->id;
        $order->payment_type = $request->payment_type;
        $order->payment_status = 'unpaid';
        $order->final_price = $car->price + $selectedOptions->sum('price');
        $order->status = 'created';
        $order->save();

        // Меняем статус автомобиля на «Забронирован»
        $car->status = 'reserved';
        $car->save();

        // Если оплата кредитная — создаём связанную кредитную заявку (1:1)
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

        DB::commit();

        return redirect()->route('profile.index')
            ->with('success', 'Автомобиль успешно зарезервирован! Менеджер свяжется с вами в ближайшее время.');
    }
}
```

**Описание.** Метод `store` реализует полный сценарий оформления заказа. Сначала проверяется валидность данных формы, а при выборе кредитного типа оплаты — дополнительные поля кредитной заявки. Проверяется доступность автомобиля — если он уже забронирован или продан, оформление отклоняется. Итоговая цена формируется как сумма базовой цены автомобиля и выбранных дополнительных опций. Весь процесс выполняется внутри транзакции БД: это гарантирует, что либо заказ создан и автомобиль забронирован одновременно, либо не выполнится ни одно действие. При кредитной оплате по формуле аннуитетного платежа рассчитывается ежемесячный платёж, и создаётся связанная запись `credit_applications` (связь 1:1 с заказом). Автомобиль автоматически получает статус `reserved`.

---

## 7. Личный кабинет клиента

**Реализовано:** просмотр всех заказов авторизованного пользователя и их статусов.

**Путь:** `app/Http/Controllers/ProfileController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $orders = Order::with(['car.model.brand', 'car.bodyType', 'creditApplication'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('profile', compact('user', 'orders'));
    }
}
```

**Описание.** Контроллер отображает личный кабинет. Получается текущий пользователь через `Auth::user()`, затем загружаются все его заказы вместе с информацией об автомобиле (марка, модель, тип кузова) и кредитной заявке (при наличии). Заказы сортируются от новых к старым. Данные передаются в представление `profile`, где клиент видит историю своих сделок и их текущий статус.

---

## 8. Администрирование: заявки и отчётность

**Реализовано:** панель администратора с перечнем заявок, показателями продаж и обновлением статусов.

**Путь:** `app/Http/Controllers/AdminController.php` (методы `index` и `updateStatus`)

```php
public function index()
{
    // Вывод списка всех заявок
    $orders = Order::with(['car.model.brand', 'user', 'creditApplication'])
        ->orderBy('created_at', 'desc')
        ->get();

    // Финансовый отчет по подтверждённым сделкам
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

    // Если сделка отклонена — возвращаем авто в статус «в наличии»
    if ($request->status === 'rejected') {
        $car = Car::find($order->car_id);
        if ($car) {
            $car->status = 'available';
            $car->save();
        }
    }

    return redirect()->back()->with('success', 'Статус сделки успешно обновлен!');
}
```

**Описание.** Метод `index` формирует рабочее окно администратора: список заказов (с предзагрузкой автомобиля, пользователя и кредитной заявки), а также два итоговых показателя — сумма продаж по подтверждённым заказам (`sum('final_price')`) и их количество (`count()`). Метод `updateStatus` обрабатывает изменение статуса заявки. Валидация ограничивает допустимые статусы заказа и статусы оплаты. Важная бизнес-логика: если сделка отклонена, автомобиль возвращается в статус `available` («в наличии»), чтобы снова быть доступным для покупки.

---

## 9. Администрирование: управление автомобилями

**Реализовано:** CRUD-операции над автомобилями, загрузка изображений, привязка опций.

**Путь:** `app/Http/Controllers/AdminController.php` (методы управления автомобилями)

```php
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

    // Проверяем, что выбранная модель относится к выбранной марке
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
        $file->storeAs('public/images/cars', $name);
        $imageUrl = \Illuminate\Support\Facades\Storage::url('images/cars/' . $name);

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

    // Запрет удаления автомобиля с активными заказами
    if (Order::where('car_id', $id)->where('status', '!=', 'rejected')->exists()) {
        return redirect()->back()->with('error', 'Нельзя удалить автомобиль, на который оформлен активный заказ!');
    }

    $car->delete();
    return redirect()->back()->with('success', 'Автомобиль успешно удален из базы данных.');
}
```

**Описание.** Блок администрирования автомобилей реализует полный CRUD. Метод `carValidate` проверяет все поля формы, включая корректность ENUM-значений (КПП, тип привода, топлива, статус), соответствие модели выбранной марке и параметры загружаемых изображений (тип JPG/JPEG/PNG/WEBP до 5 МБ). Метод `handleImages` сохраняет загруженные файлы в публичное хранилище `storage/app/public/images/cars/` и создаёт запись изображения в таблице `images` с URL для отображения. Метод `carStore` создаёт автомобиль и привязывает опции и изображения. Метод `carDestroy` удаляет автомобиль только при отсутствии активных заказов — это защита целостности данных (нельзя удалить проданную или зарезервированную машину).

---

## 10. Сидеры

### 10.1. Пользователь

**Реализовано:** создание администратора по умолчанию.

**Путь:** `database/seeders/UserSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

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

**Описание.** Создаёт учётную запись администратора с логином `admin@autogarant.ru`. Пароль хэшируется. Использование `updateOrCreate` делает сидер идемпотентным: при повторном запуске существующая запись обновляется, а не вызывает ошибку дубликата.

### 10.2. Марки и модели

**Реализовано:** создание справочника марок и моделей автомобилей.

**Путь:** `database/seeders/BrandAndModelSeeder.php`

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

**Описание.** Наполняет таблицы `brands` и `models`: для каждой марки создаётся запись, и через отношение `models()` — соответствующие модели автомобилей (связь «один-ко-многим»).

### 10.3. Типы кузова и опции

**Путь:** `database/seeders/BodyTypeSeeder.php` и `database/seeders/OptionSeeder.php`

```php
<?php

// BodyTypeSeeder
namespace Database\Seeders;

use App\Models\BodyType;
use Illuminate\Database\Seeder;

class BodyTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['Седан', 'Внедорожник', 'Хэтчбек', 'Кроссовер', 'Универсал'];
        foreach ($types as $type) {
            BodyType::create(['name' => $type]);
        }
    }
}
```

```php
<?php

// OptionSeeder
namespace Database\Seeders;

use App\Models\Option;
use Illuminate\Database\Seeder;

class OptionSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            ['name' => 'Зимний пакет (подогрев руля и лобового)', 'price' => 45000],
            ['name' => 'Панорамная крыша', 'price' => 120000],
            ['name' => 'Камера 360 градусов', 'price' => 60000],
            ['name' => 'Кожаный салон', 'price' => 95000],
        ];
        foreach ($options as $option) {
            Option::create($option);
        }
    }
}
```

**Описание.** Заполняют справочники типов кузова (5 значений) и дополнительных опций (4 значения с ценами), которые используются при генерации автомобилей и в формах оформления заказа.

### 10.4. Автомобили

**Реализовано:** генерация 11 тестовых автомобилей со случайными характеристиками, опциями и демонстрационными заказами.

**Путь:** `database/seeders/CarSeeder.php`

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

            $carOptions = $options->random(rand(1, 2));
            foreach ($carOptions as $option) {
                $car->options()->attach($option->id, ['count' => 1]);
            }

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

                if ($order->payment_type === 'credit') {
                    $downPayment = $order->final_price * 0.20;
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

**Описание.** Сидер генерирует 11 автомобилей, случайно выбирая модель, тип кузова, год выпуска, цену, объём двигателя, КПП, тип привода, топливо и статус. Каждому автомобилю привязываются 1–2 случайные опции (связь «многие-ко-многим»). Для демонстрации работы заказов создаются заказы на часть автомобилей со статусами reserved/sold, а при кредитном типе оплаты — связанная кредитная заявка.

### 10.5. Изображения

**Реализовано:** привязка корректных фотографий к автомобилям по моделям.

**Путь:** `database/seeders/ImageSeeder.php`

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

**Описание.** Сидер сопоставляет физические файлы изображений (`1.jpg`…`11.jpg` из хранилища) с конкретными моделями автомобилей согласно внутреннему массиву `modelImages`. Перед назначением старые записи изображений удаляются для исключения дублей. Если точная модель не найдена, подставляется фото той же марки, иначе — изображение по умолчанию. Для некоторых моделей (например, Chery Tiggo) назначено по несколько фото.

### 10.6. Общий запуск

**Путь:** `database/seeders/DatabaseSeeder.php`

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

**Описание.** Центральный сидер определяет порядок наполнения базы данных. Порядок критичен: сначала пользователи, затем справочники (марки, модели, типы кузова, опции), потом автомобили (зависят от справочников), и в последнюю очередь — изображения (зависят от созданных автомобилей). Запускается командой `php artisan migrate:fresh --seed`.

---

## 11. UI-компоненты Blade

**Реализовано:** переиспользуемые компоненты интерфейса для единообразных форм.

**Путь:** `resources/views/components/ui/checkbox.blade.php`

```blade
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

**Описание.** Компонент инкапсулирует разметку checkbox Bootstrap. Принимает параметры: `name` (обязательный), `value`, необязательный `label`, флаг `checked`, идентификатор `id`, классы и флаг `disabled`. При отсутствии `id` он генерируется автоматически (`checkbox-{name}-{уникальный_суффикс}`). Компонент автоматически выводит сообщение об ошибке валидации через директиву `@error`, а при `label` — связанную подпись. Вынос в компонент устраняет дублирование кода в формах авторизации, заказа и администрирования.

**Путь:** `resources/views/components/ui/input.blade.php`

```blade
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

**Описание.** Универсальный компонент текстового поля формы. Поддерживает тип поля (`text`, `email`, `password`, `tel`), подпись с маркером обязательности, placeholder, стартовое значение (с учётом старого ввода формы через `old()`), состояния `disabled`/`readonly`, настройку автозаполнения. Выводит сообщения об ошибках валидации в стандартном для Bootstrap блоке `invalid-feedback`. Используется в формах регистрации и входа вместо повторяющейся hand-written разметки.