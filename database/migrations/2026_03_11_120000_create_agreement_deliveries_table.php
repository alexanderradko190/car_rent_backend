<?php

use App\Enums\Report\ReportStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agreement_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rental_request_id');
            $table->unsignedBigInteger('rent_history_id')->nullable();
            $table->unsignedBigInteger('car_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('status', 20)->default(ReportStatus::PENDING->value);
            $table->string('agreement_path')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agreement_deliveries');
    }
};
