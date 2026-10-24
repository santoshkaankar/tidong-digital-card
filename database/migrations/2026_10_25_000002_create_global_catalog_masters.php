<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        foreach (['brands','item_categories','units','variant_attributes','variant_values'] as $name) {
            Schema::create('global_catalog_'.$name, function (Blueprint $table) use ($name) {
                $table->id();
                $table->string('name',150);
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->string('code',50)->nullable();
                $table->string('owner_name',200)->nullable();
                $table->string('image_path')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by_user_id')->nullable();
                $table->timestamps();
                $table->unique(['name','parent_id']);
                $table->index('parent_id');
            });
        }
    }
    public function down(): void {
        foreach (['variant_values','variant_attributes','units','item_categories','brands'] as $name) Schema::dropIfExists('global_catalog_'.$name);
    }
};
