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
    Schema::table('users', function (Blueprint $table) {
        // Mobile ke baad username column add ho jayega (agar pehle se na ho)
        if (!Schema::hasColumn('users', 'username')) {
            $table->string('username')->nullable()->unique()->after('mobile');
        }
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('username');
    });
}
};
