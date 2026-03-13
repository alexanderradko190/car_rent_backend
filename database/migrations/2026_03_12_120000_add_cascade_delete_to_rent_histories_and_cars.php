<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rent_histories', function (Blueprint $table) {
            $table->dropForeign(['car_id']);
            $table->dropForeign(['client_id']);
        });

        Schema::table('rent_histories', function (Blueprint $table) {
            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->dropForeign(['current_renter_id']);
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->foreign('current_renter_id')->references('id')->on('clients')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('rent_histories', function (Blueprint $table) {
            $table->dropForeign(['car_id']);
            $table->dropForeign(['client_id']);
        });

        Schema::table('rent_histories', function (Blueprint $table) {
            $table->foreign('car_id')->references('id')->on('cars');
            $table->foreign('client_id')->references('id')->on('clients');
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->dropForeign(['current_renter_id']);
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->foreign('current_renter_id')->references('id')->on('clients');
        });
    }
};
