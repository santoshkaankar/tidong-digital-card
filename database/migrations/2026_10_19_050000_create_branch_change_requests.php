<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('branch_change_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->restrictOnDelete();
            $table->string('change_type', 40); // branch, license_create, license_update
            $table->foreignId('branch_license_id')->nullable()->constrained('branch_licenses')->restrictOnDelete();
            $table->json('old_values');
            $table->json('proposed_values');
            $table->string('status', 20)->default('pending');
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_reason')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['branch_id', 'status']);
            $table->index(['vendor_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('branch_change_requests'); }
};
