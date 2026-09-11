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
        Schema::create('taxi_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('booking_number')->unique();
            $table->unsignedBigInteger('user_id')->nullable()->index('taxi_bookings_user_id_foreign');
            $table->text('pickup_address')->nullable();
            $table->text('drop_address')->nullable();
            $table->decimal('pickup_latitude', 10, 8)->nullable();
            $table->decimal('pickup_longitude', 11, 8)->nullable();
            $table->decimal('drop_latitude', 10, 8)->nullable();
            $table->decimal('drop_longitude', 11, 8)->nullable();
            $table->unsignedBigInteger('vendor_id')->index('taxi_bookings_vendor_id_foreign');
            $table->unsignedBigInteger('taxi_id')->index('taxi_bookings_taxi_id_foreign');
            $table->string('pickup_location');
            $table->string('drop_location');
            $table->dateTime('pickup_datetime');
            $table->decimal('total_distance_km')->default(0);
            $table->integer('estimated_duration_mins')->nullable();
            $table->decimal('total_amount', 10)->default(0);
            $table->integer('total_duration_mins')->default(0);
            $table->decimal('base_fare')->default(0);
            $table->decimal('distance_fare')->default(0);
            $table->decimal('toll_charges')->default(0);
            $table->decimal('night_charges')->default(0);
            $table->decimal('tip_amount')->default(0);
            $table->decimal('grand_total', 10)->default(0);
            $table->enum('booking_status', ['pending', 'confirmed', 'ongoing', 'completed', 'cancelled'])->default('pending');
            $table->text('cancellation_reason')->nullable();
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('payment_mode')->default('cash');
            $table->string('payment_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxi_bookings');
    }
};
