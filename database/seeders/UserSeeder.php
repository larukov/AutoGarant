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
            'name' => 'Ирина М. (Менеджер)',
            'email' => 'admin@auto.ru',
            'password' => Hash::make('admin123'),
            'phone' => '+79991112233',
            'is_admin' => true,
            'status' => true,
        ]);

        User::create([
            'name' => 'Дмитрий Л. (Клиент)',
            'email' => 'client@auto.ru',
            'password' => Hash::make('client123'),
            'phone' => '+79994445566',
            'is_admin' => false,
            'status' => true,
        ]);
    }
}
