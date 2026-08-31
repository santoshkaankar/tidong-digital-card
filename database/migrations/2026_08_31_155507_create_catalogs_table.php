<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // डेटाबेस-न्यूट्रल तरीक़ा (MySQL और Supabase PostgreSQL दोनों पर काम करेगा)
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
        Schema::enableForeignKeyConstraints();

        // नई कैटलॉग टेबल
        Schema::create('catalogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('address'); // Table No, Room No, या Location
            $table->string('slug')->unique();
            $table->json('item_ids')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogs');
    }
};