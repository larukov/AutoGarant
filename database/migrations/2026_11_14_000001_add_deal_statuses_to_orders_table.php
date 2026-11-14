<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'created',
                'confirmed',
                'prepaid',
                'delivered',
                'closed',
                'rejected',
            ])->default('created')->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['created', 'confirmed', 'rejected'])->default('created')->change();
        });
    }
};