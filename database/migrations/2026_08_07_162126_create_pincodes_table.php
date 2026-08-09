<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pincodes', function (Blueprint $table) {
            $table->id();
            $table->string('circle_name')->nullable();
            $table->string('region_name')->nullable();
            $table->string('division_name')->nullable();
            $table->string('office_name');
            $table->string('pincode', 10)->index();
            $table->string('office_type')->nullable();
            $table->string('delivery_status')->nullable();
            $table->string('district')->nullable();
            $table->string('state_name')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pincodes');
    }
};