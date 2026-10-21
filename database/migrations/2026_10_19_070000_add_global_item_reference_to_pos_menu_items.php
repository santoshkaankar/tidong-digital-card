<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_menu_items', function (Blueprint $table) {
            // Existing POS items remain intact and default to branch-private items.
            $table->foreignId('global_menu_item_id')->nullable()->constrained('global_menu_items')->restrictOnDelete();
            $table->string('source_kind', 16)->default('custom');
            $table->string('category',80)->nullable();
            $table->string('photo_path',255)->nullable();
            $table->unique(['branch_id','global_menu_item_id'], 'pos_menu_branch_global_unique');
        });
    }

    public function down(): void
    {
        throw new RuntimeException('POS menu records and their historic order references must not be dropped. Automatic rollback disabled.');
    }
};
