<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use Notifiable;

    // 1. Отключаем автоматический парный механизм (created_at + updated_at)
    public $timestamps = false;

    // 2. Указываем, что колонка создания всё же существует и должна заполняться
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'is_admin', 'status'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'status' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Пользователь может делать много заказов (1:M)
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }
}
