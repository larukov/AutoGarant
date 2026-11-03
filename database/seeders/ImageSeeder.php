<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarModel;
use App\Models\Brand;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    // Соответствие модели файлам картинок в storage/app/public/images/cars/
    // (согласно ATTRIBUTION.txt). Ключи — имена моделей из BrandAndModelSeeder.
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
            // Ранее созданные записи лучше удалить, чтобы не задваивать
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

        // Точная модель — свои фото; иначе фото той же марки; иначе первое
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