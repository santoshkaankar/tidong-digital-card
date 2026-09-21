<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiffin_catalogs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id')->nullable(); // Agar multi-vendor hai
            $table->string('title'); // Jaise: Standard Mess Tiffin
            $table->string('delivery_address'); // Table / Room No / Area
            $table->boolean('is_active')->default(true); // Tiffin On/Off toggle
            
            // Pricing structure
            $table->decimal('single_day_rate', 10, 2)->default(0);
            $table->decimal('full_week_rate', 10, 2)->default(0);
            $table->decimal('full_month_rate', 10, 2)->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiffin_catalogs');
    }
};