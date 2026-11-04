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
        $order->final_price = $car->price + $selectedOptions->sum('price'); // Базовая цена + выбранные опции
        $order->status = 'created';        // Начальный статус сделки (Заявка оформлена)
        $order->save();

        // Меняем статус самого автомобиля на "Забронирован" (reserved)
        $car->status = 'reserved';
        $car->save();

        // 5. Если выбран тип оплаты "Кредит", создаем связанную запись в credit_applications (1:1 по ТЗ)
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
