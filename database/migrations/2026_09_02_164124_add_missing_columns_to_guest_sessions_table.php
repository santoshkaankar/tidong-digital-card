<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_sessions', function (Blueprint $table) {
            // जो कॉलम टेबल में नहीं हैं, सिर्फ उन्हें जोड़ें
            if (!Schema::hasColumn('guest_sessions', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('guest_sessions', 'last_active_at')) {
                $table->timestamp('last_active_at')->nullable()->after('user_agent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guest_sessions', function (Blueprint $table) {
            $table->dropColumn(['user_agent', 'last_active_at']);
        });
    }
};