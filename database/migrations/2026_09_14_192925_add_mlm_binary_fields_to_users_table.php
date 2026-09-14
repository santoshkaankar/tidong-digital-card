<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMlmBinaryFieldsToUsersTable extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('users', 'sponsor_id')) {
                $table->unsignedBigInteger('sponsor_id')->nullable()->after('username');
            }
            if (!Schema::hasColumn('users', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('sponsor_id');
            }
            if (!Schema::hasColumn('users', 'position')) {
                $table->enum('position', ['left', 'right'])->nullable()->after('parent_id');
            }
            if (!Schema::hasColumn('users', 'left_count')) {
                $table->integer('left_count')->default(0)->after('position');
            }
            if (!Schema::hasColumn('users', 'right_count')) {
                $table->integer('right_count')->default(0)->after('left_count');
            }
            if (!Schema::hasColumn('users', 'mlm_status')) {
                $table->tinyInteger('mlm_status')->default(0)->comment('0=Inactive, 1=Active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username', 
                'sponsor_id', 
                'parent_id', 
                'position', 
                'left_count', 
                'right_count', 
                'mlm_status'
            ]);
        });
    }
}