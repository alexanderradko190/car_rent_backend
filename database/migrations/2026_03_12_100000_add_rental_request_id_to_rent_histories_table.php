<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rent_histories', function (Blueprint $table) {
            $table->foreignId('rental_request_id')->nullable()->after('id')->constrained('rental_requests')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rent_histories', function (Blueprint $table) {
            $table->dropForeign(['rental_request_id']);
        });
    }
};
