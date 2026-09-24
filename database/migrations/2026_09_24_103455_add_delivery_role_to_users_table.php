<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Database ke ENUM column me 'delivery' role ko add karne ke liye query
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'member', 'vendor', 'business', 'employee', 'delivery') DEFAULT 'member'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'member', 'vendor', 'business', 'employee') DEFAULT 'member'");
    }
};