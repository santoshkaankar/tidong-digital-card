<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vendor_categories', function (Blueprint $table) {
            // Agar user_id column pehle se nahi hai tabhi add karega
            if (!Schema::hasColumn('vendor_categories', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendor_categories', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};