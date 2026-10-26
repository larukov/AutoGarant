<?php

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