<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Option extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'price'];

    public function cars(): BelongsToMany
    {
         return $this->belongsToMany(Car::class, 'car_option', 'options_id', 'car_id')
                ->withPivot('count');
    }
}
