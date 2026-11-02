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
    /**
     * Создание новой заявки на бронирование автомобиля.
     */
    public function store(Request $request)
    {
        // 1. Валидация входящих данных из формы модального окна
        $request->validate([
            'car_id' => 'required|exists:cars,id',
            'payment_type' => 'required|in:cash,card,credit',
        ]);

        // 2. Находим автомобиль по ID
        $car = Car::findOrFail($request->car_id);

        // 3. Проверяем бизнес-ограничение: доступна ли машина для заказа
        if ($car->status !== 'available') {
            return redirect()->back()->with('error', 'К сожалению, этот автомобиль уже забронирован или продан.');
        }

        // 4. Используем транзакцию базы данных, чтобы гарантировать целостность данных
        DB::beginTransaction();

        // Создаем запись заказа в таблице orders
        $order = new Order();
        $order->user_id = Auth::id(); // ID текущего авторизованного клиента
        $order->car_id = $car->id;
        $order->payment_type = $request->payment_type;
        $order->payment_status = 'unpaid'; // По умолчанию неоплачено
        $order->final_price = $car->price;  // Фиксируем стоимость авто на момент заказа
        $order->status = 'created';        // Начальный статус сделки (Заявка оформлена)
        $order->save();

        // Меняем статус самого автомобиля на "Забронирован" (reserved)
        $car->status = 'reserved';
        $car->save();

        // 5. Если выбран тип оплаты "Кредит", создаем связанную запись в credit_applications (1:1 по ТЗ)
        if ($request->payment_type === 'credit') {
            $credit = new CreditApplication();
            $credit->order_id = $order->id;
            $credit->down_payment = 0;
            $credit->loan_term_months = 12;
            $credit->interest_rate = 11.5;
            $credit->monthly_payment = 0;
            $credit->save();
        }

        // Фиксируем изменения в БД
        DB::commit();

        return redirect()->route('profile.index')
            ->with('success', 'Автомобиль успешно зарезервирован! Менеджер свяжется с вами в ближайшее время. Отслеживать статус можно в личном кабинете.');
    }
}
