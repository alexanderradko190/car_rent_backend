<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\RentalStatus;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rental_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('car_id')->constrained('cars')->onDelete('cascade');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->boolean('insurance_option')->default(false);
            $table->string('status', 20)->default(RentalStatus::PENDING->value);
            $table->string('agreement_path')->nullable();
            $table->timestamps();

            $table->index(['car_id', 'start_time', 'end_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_requests');
    }
};
