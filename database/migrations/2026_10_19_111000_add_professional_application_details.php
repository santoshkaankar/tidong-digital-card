<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('legal_professional_qualifications', function (Blueprint $table) {
            $table->string('registration_reference', 120)->nullable();
            $table->text('applicant_note')->nullable();
            $table->string('verification_reference', 250)->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->string('review_reason', 500)->nullable();
        });
    }
    public function down(): void
    {
        Schema::table('legal_professional_qualifications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn(['registration_reference', 'applicant_note', 'verification_reference', 'verified_at', 'review_reason']);
        });
    }
};
