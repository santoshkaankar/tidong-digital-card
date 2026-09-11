<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // <-- bigIncrements की जगह id() रखें
            $table->string('name');
            $table->string('slug')->nullable()->unique();
            $table->string('email')->unique();
            $table->string('payment_qr')->nullable();
            $table->string('mobile')->nullable()->unique();
            $table->string('username')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('member');
            $table->string('vehicle_no')->nullable();
            $table->string('license_no')->nullable();
            $table->string('status')->default('pending');
            $table->string('business_type')->nullable();
            $table->enum('duty_status', ['online', 'offline', 'busy'])->default('offline');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};