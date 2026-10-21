<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('restaurant_dining_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->string('label', 60);
            $table->string('qr_token', 64)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['branch_id','label']);
            $table->index(['branch_id','is_active']);
        });
    }
    public function down(): void {
        throw new \RuntimeException('Do not automatically remove dining-table QR history. Contact a developer for a retention-safe rollback.');
    }
};
