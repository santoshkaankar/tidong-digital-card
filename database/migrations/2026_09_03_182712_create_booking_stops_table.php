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
        Schema::create('booking_stops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('taxi_booking_id')->index('booking_stops_taxi_booking_id_foreign');
            $table->unsignedBigInteger('tourist_spot_id')->index('booking_stops_tourist_spot_id_foreign');
            $table->integer('stop_order')->default(1);
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('stop_duration_mins')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_stops');
    }
};
