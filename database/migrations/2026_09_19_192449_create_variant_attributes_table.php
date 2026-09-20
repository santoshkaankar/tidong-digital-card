<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Master list of attributes (e.g., Color -> Red, Blue | Size -> S, M, L, XL)
        Schema::create('variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('attribute_name'); // Jaise: Color, Size, Material
            $table->string('attribute_value'); // Jaise: Red, XL, Cotton
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('variant_attributes');
    }
};