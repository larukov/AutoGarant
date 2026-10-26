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

        // Массивы для генерации случайных ENUM-данных
        $transmissions = ['manual', 'automatic', 'robot'];
        $driveTypes = ['FWD', 'RWD', 'AWD'];
        $fuelTypes = ['petrol', 'diesel', 'electric', 'hybrid'];
        $statuses = ['available', 'available', 'available', 'reserved', 'sold']; // Больше шансов на 'available'

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

            // 1. Создаем запись изображения. Путь ведет в public/images/cars/
            // Физически файлы должны лежать по адресу: public/images/cars/1.jpg, public/images/cars/2.jpg и т.д.
            $car->images()->create([
                'name' => "Фото автомобиля " . $car->id,
                'image_url' => "/images/cars/{$i}.jpg"
            ]);

            // 2. Привязываем 1-2 случайные дополнительные опции (связь Многие-ко-многим)
            $carOptions = $options->random(rand(1, 2));
            foreach ($carOptions as $option) {
                $car->options()->attach($option->id, ['count' => 1]);
            }

            // 3. Для демонстрации: если статус машины 'reserved' или 'sold', создадим заказ
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
