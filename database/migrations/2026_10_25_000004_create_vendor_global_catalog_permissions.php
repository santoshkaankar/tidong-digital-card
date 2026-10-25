<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('vendor_global_catalog_permissions',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('vendor_id')->unique();
   $t->boolean('can_create')->default(false);
   $t->boolean('can_edit')->default(false);
   $t->unsignedBigInteger('updated_by_user_id')->nullable();$t->timestamps();
  });
 }
 public function down(): void { Schema::dropIfExists('vendor_global_catalog_permissions'); }
};
