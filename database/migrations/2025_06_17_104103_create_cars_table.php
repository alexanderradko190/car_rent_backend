<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('make', 50);
            $table->string('model', 50);
            $table->smallInteger('year');
            $table->string('vin', 17)->unique();
            $table->string('license_plate', 15)->unique();
            $table->string('car_class', 20);
            $table->smallInteger('power')->nullable();
            $table->float('hourly_rate', 8, 2);
            $table->string('status', 20)->default('available');
            $table->foreignId('current_renter_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
