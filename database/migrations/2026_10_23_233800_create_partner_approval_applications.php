<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_approval_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('account_type', 30); // professional OR service_provider, never both
            $table->string('service_title', 160);
            $table->string('category', 120);
            $table->string('registration_reference', 160);
            $table->text('experience_description');
            $table->string('status', 24)->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('verification_reference', 250)->nullable();
            $table->string('review_reason', 500)->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'account_type'], 'partner_review_user_type_uniq');
            $table->index(['account_type', 'status', 'id'], 'partner_review_queue_idx');
        });
    }

    public function down(): void
    {
        // Approval evidence must not disappear through a routine rollback.
        throw new RuntimeException('Approval history requires explicit data-retention review.');
    }
};