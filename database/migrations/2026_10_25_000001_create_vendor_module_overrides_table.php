<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendor_module_overrides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->string('module_code', 100);
            $table->boolean('is_enabled')->default(true);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unique(['vendor_id', 'module_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_module_overrides');
    }
};
