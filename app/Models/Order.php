<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'car_id', 'payment_type', 
        'payment_status', 'final_price', 'status'
    ];

    protected $casts = [
        'final_price' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    // Заказ может иметь одну кредитную заявку (1:1)
    public function creditApplication(): HasOne
    {
        return $this->hasOne(CreditApplication::class, 'order_id');
    }
}
