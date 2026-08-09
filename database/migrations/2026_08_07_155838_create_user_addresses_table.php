<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('house_no_mohalla'); // H.No., Building, Mohalla / Village
            $table->string('pincode');          // Pincode
            $table->string('area_name')->nullable();  // Area / Sub office
            $table->string('city')->nullable();       // City
            $table->string('district')->nullable();   // District
            $table->string('state')->nullable();      // State
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};