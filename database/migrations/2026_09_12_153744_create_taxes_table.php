<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('taxes')) {
            Schema::create('taxes', function (Blueprint $table) {
                $table->id();
                $table->string('tax_name');
                $table->decimal('tax_percentage', 5, 2)->default(0);
                $table->decimal('cess_percentage', 5, 2)->default(0);
                $table->string('tax_type')->default('GST');
                $table->boolean('is_active')->default(1);
                $table->text('remark')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('taxes');
    }
};