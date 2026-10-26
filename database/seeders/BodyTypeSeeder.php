<?php

namespace Database\Seeders;
use App\Models\BodyType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BodyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['Седан', 'Внедорожник', 'Хэтчбек', 'Кроссовер', 'Универсал'];
        foreach ($types as $type) {
            BodyType::create(['name' => $type]);
        }
    }
}
