<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('legal_feature_flags', function (Blueprint $t) {
            $t->id();
            $t->string('code', 80)->unique();
            $t->boolean('enabled')->default(false);
            $t->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });
        Schema::create('legal_professional_profiles', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained('users')->restrictOnDelete();
            $t->string('display_name');
            $t->string('verification_status', 30)->default('pending');
            $t->timestamps();
        });
        Schema::create('legal_professional_qualifications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('professional_id')->constrained('legal_professional_profiles')->restrictOnDelete();
            $t->string('category', 40);
            $t->string('verification_status', 30)->default('pending');
            $t->timestamps();
            $t->unique(['professional_id','category'], 'legal_qualification_prof_category_uniq');
        });
        Schema::create('legal_engagements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('vendor_id')->constrained('vendors')->restrictOnDelete();
            $t->foreignId('professional_id')->constrained('legal_professional_profiles')->restrictOnDelete();
            $t->foreignId('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $t->foreignId('branch_id')->nullable()->constrained('branches')->restrictOnDelete();
            $t->string('category', 40);
            $t->string('scope', 30)->default('vendor');
            $t->string('status', 30)->default('invited');
            $t->json('permissions');
            $t->foreignId('invited_by')->constrained('users')->restrictOnDelete();
            $t->timestamp('accepted_at')->nullable();
            $t->timestamp('ended_at')->nullable();
            $t->timestamps();
            $t->index(['vendor_id','status']);
            $t->index(['professional_id','status']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('legal_engagements');
        Schema::dropIfExists('legal_professional_qualifications');
        Schema::dropIfExists('legal_professional_profiles');
        Schema::dropIfExists('legal_feature_flags');
    }
};
