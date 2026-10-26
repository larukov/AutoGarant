<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\CarModel;
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
