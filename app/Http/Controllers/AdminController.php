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
        // 1. Вывод списка всех заявок в реальном времени
        $orders = Order::with(['car.model.brand', 'user', 'creditApplication'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Формирование финансового отчета по закрытым сделкам (статус 'closed' или аналогичный успешный)
        $totalSales = Order::where('status', 'confirmed')->sum('final_price');
        $closedDealsCount = Order::where('status', 'confirmed')->count();

        return view('admin.index', compact('orders', 'totalSales', 'closedDealsCount'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:created,confirmed,rejected', // Валидация под ваши ENUM
            'payment_status' => 'required|in:unpaid,10%,50%,fully_paid',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->payment_status = $request->payment_status;
        $order->save();

        // Бизнес-логика: если сделка отклонена менеджером, возвращаем авто статус "в наличии"
        if ($request->status === 'rejected') {
            $car = Car::find($order->car_id);
            if ($car) {
                $car->status = 'available';
                $car->save();
            }
        }

        return redirect()->back()->with('success', 'Статус сделки успешно обновлен!');
    }
    public function carsIndex()
    {
        $cars = Car::with(['model.brand', 'bodyType', 'images'])->orderBy('id', 'desc')->get();

        return view('admin.cars.index', compact('cars'));
    }

    public function carCreate()
    {
        $brands = Brand::with('models')->orderBy('name')->get();
        $bodyTypes = BodyType::orderBy('name')->get();
        $options = Option::orderBy('name')->get();

        return view('admin.cars.create', compact('brands', 'bodyTypes', 'options'));
    }

    public function carEdit($id)
    {
        $car = Car::with(['images', 'options'])->findOrFail($id);
        $brands = Brand::with('models')->orderBy('name')->get();
        $bodyTypes = BodyType::orderBy('name')->get();
        $options = Option::orderBy('name')->get();
        $optionIds = $car->options->pluck('id')->toArray();

        return view('admin.cars.edit', compact('car', 'brands', 'bodyTypes', 'options', 'optionIds'));
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
            'engine_volume.max' => 'Объём двигателя не может превышать 99.9 л (не более двух знаков до запятой).',
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

    protected function handleOptionSelection(Car $car, $optionIds)
    {
        $car->options()->sync(is_array($optionIds) ? $optionIds : []);
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

    public function carUpdate(Request $request, $id)
    {
        $car = Car::findOrFail($id);

        $data = $this->carValidate($request);

        $car->update($data);

        $this->handleOptionSelection($car, $data['options'] ?? []);
        $this->handleImages($request, $car);

        return redirect()->route('admin.cars.index')->with('success', 'Данные автомобиля успешно изменены!');
    }

    public function imageDestroy(Request $request, $id)
    {
        $image = Image::findOrFail($id);

        if ($image->car_id !== (int) $request->car_id) {
            abort(403);
        }

        $image->delete();

        return redirect()->back()->with('success', 'Изображение удалено.');
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
