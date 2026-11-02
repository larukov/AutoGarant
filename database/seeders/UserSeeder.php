<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Ирина',
            'email' => 'admin@auto.ru',
            'password' => Hash::make('admin123'),
            'tel' => '+79991112233',
            'is_admin' => true,
            'status' => true,
        ]);

        User::create([
            'name' => 'Дмитрий',
            'email' => 'client@auto.ru',
            'password' => Hash::make('client123'),
            'tel' => '+79994445566',
            'is_admin' => false,
            'status' => true,
        ]);
    }
}
