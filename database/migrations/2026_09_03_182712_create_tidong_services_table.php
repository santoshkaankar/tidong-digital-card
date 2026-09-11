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
        Schema::create('tidong_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('service_code')->unique();
            $table->string('name_en');
            $table->string('name_hi');
            $table->string('icon_class');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tidong_services');
    }
};
