<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Car extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'model_id', 'year', 'price', 'body_type_id', 
        'engine_volume', 'transmission', 'drive_type', 
        'fuel_type', 'status'
    ];

    // Приведение типов данных
    protected $casts = [
        'price' => 'decimal:2',
        'engine_volume' => 'decimal:1',
    ];

    public function model(): BelongsTo
    {
        return $this->belongsTo(CarModel::class, 'model_id');
    }

    public function bodyType(): BelongsTo
    {
        return $this->belongsTo(BodyType::class, 'body_type_id');
    }

    // У одной машины может быть галерея изображений (1:M)
    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'car_id');
    }

    // Связь Многие-ко-многим с опциями через сводную таблицу cars-options
    public function options(): BelongsToMany
    {
        return $this->belongsToMany(Option::class, 'car_option', 'car_id', 'options_id')
                ->withPivot('count');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'car_id');
    }
}
