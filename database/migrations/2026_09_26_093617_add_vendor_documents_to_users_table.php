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
            $table->string('fssai_number')->nullable();
            $table->string('fssai_certificate')->nullable();
            $table->string('gst_certificate')->nullable();
            $table->string('mca_certificate')->nullable();
            $table->string('upi_qr_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'fssai_number',
                'fssai_certificate',
                'gst_certificate',
                'mca_certificate',
                'upi_qr_code'
            ]);
        });
    }
};