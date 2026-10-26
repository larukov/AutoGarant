<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Создаем таблицу с именем car_option
        Schema::create('car_option', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('car_id')->constrained('cars')->onDelete('cascade');
            
            $table->foreignId('options_id')->constrained('options')->onDelete('cascade');
            
            $table->integer('count')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_option');
    }
};
