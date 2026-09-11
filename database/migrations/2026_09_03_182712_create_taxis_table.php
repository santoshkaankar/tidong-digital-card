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
        Schema::create('taxis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('taxis_user_id_foreign');
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('vehicle_name');
            $table->string('vehicle_number');
            $table->string('vehicle_color')->nullable();
            $table->enum('vehicle_type', ['Hatchback', 'Sedan', 'SUV', 'Tempo Traveller'])->default('Sedan');
            $table->enum('ac_type', ['ac', 'non_ac'])->default('ac');
            $table->string('fuel_type')->nullable();
            $table->integer('passenger_capacity')->default(4);
            $table->integer('luggage_capacity')->default(2);
            $table->decimal('rate_per_km')->default(12);
            $table->decimal('base_fare')->default(500);
            $table->decimal('night_charge')->default(300);
            $table->string('image')->nullable();
            $table->enum('status', ['available', 'busy', 'maintenance'])->default('available');
            $table->timestamps();
            $table->string('vehicle_model')->nullable();
            $table->integer('seating_capacity')->default(4);
            $table->decimal('per_km_rate')->default(0);
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxis');
    }
};
