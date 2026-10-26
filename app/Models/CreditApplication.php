<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditApplication extends Model
{
    protected $table = 'credit_applications';
    public $timestamps = false;

    protected $fillable = [
        'order_id', 'down_payment', 'loan_term_months', 
        'interest_rate', 'monthly_payment'
    ];

    protected $casts = [
        'down_payment' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
