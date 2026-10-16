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
        if (!Schema::hasColumn('users', 'parent_user_id')) {
            $table->unsignedBigInteger('parent_user_id')->nullable()->after('id');
            $table->foreign('parent_user_id')->references('id')->on('users')->onDelete('cascade');
        }
        if (!Schema::hasColumn('users', 'is_child')) {
            $table->boolean('is_child')->default(false)->after('parent_user_id');
        }
        if (!Schema::hasColumn('users', 'dob')) {
            $table->date('dob')->nullable()->after('mobile');
        }
        if (!Schema::hasColumn('users', 'permissions')) {
            $table->json('permissions')->nullable()->after('is_child');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['parent_user_id']);
            $table->dropColumn(['parent_user_id', 'is_child', 'dob', 'permissions']);
        });
    }
};