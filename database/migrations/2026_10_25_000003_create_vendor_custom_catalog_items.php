<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('vendor_custom_catalog_items',function(Blueprint $t){
   $t->id();$t->foreignId('vendor_id')->index();$t->foreignId('branch_id')->index();
   $t->string('name',150);$t->text('description')->nullable();$t->decimal('mrp',12,2)->default(0);
   $t->decimal('sale_price',12,2)->default(0);$t->boolean('is_active')->default(true);$t->timestamps();
   $t->unique(['branch_id','name']);
  });
 }
 public function down(): void {Schema::dropIfExists('vendor_custom_catalog_items');}
};
