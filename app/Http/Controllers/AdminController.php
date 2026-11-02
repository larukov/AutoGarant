<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\BodyType;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Вывод списка всех заявок в реальном времени
        $orders = Order::with(['car.model.brand', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Формирование финансового отчета по закрытым сделкам (статус 'closed' или аналогичный успешный)
        $totalSales = Order::where('status', 'confirmed')->sum('final_price');
        $closedDealsCount = Order::where('status', 'confirmed')->count();

        return view('admin', compact('orders', 'totalSales', 'closedDealsCount'));
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
        $cars = Car::with(['model.brand', 'bodyType'])->orderBy('id', 'desc')->get();
        $models = CarModel::with('brand')->get();
        $bodyTypes = BodyType::all();

        return view('admin-cars', compact('cars', 'models', 'bodyTypes'));
    }
    public function carStore(Request $request)
    {
        $data = $request->validate([
            'model_id' => 'required|exists:models,id',
            'body_type_id' => 'required|exists:body_type,id',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'price' => 'required|numeric|min:0',
            'engine_volume' => 'required|numeric|min:0',
            'transmission' => 'required|in:manual,automatic,robot',
            'drive_type' => 'required|in:FWD,RWD,AWD',
            'fuel_type' => 'required|in:petrol,diesel,electric,hybrid',
            'status' => 'required|in:available,reserved,sold',
        ]);

        Car::create($data);

        return redirect()->back()->with('success', 'Новый автомобиль успешно добавлен в каталог!');
    }

    public function carUpdate(Request $request, $id)
    {
        $car = Car::findOrFail($id);

        $data = $request->validate([
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:available,reserved,sold',
            'transmission' => 'required|in:manual,automatic,robot',
        ]);

        $car->update($data);

        return redirect()->back()->with('success', 'Данные автомобиля успешно изменены!');
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
