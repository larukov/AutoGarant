<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('car_id')->constrained('cars');
            $table->enum('payment_type', ['cash', 'credit', 'card']);
            $table->enum('payment_status', ['unpaid', '10%', '50%', 'fully_paid'])->default('unpaid');
            $table->decimal('final_price', 12, 2);
            $table->enum('status', ['created', 'confirmed', 'rejected'])->default('created');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
