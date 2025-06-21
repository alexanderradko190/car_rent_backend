<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rent_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained();
            $table->foreignId('client_id')->constrained();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->float('total_cost');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('rent_histories');
    }
};
