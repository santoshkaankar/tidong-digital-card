<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('legal_work_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('engagement_id')->constrained('legal_engagements')->cascadeOnDelete();
            $table->foreignId('professional_user_id')->constrained('users')->restrictOnDelete();
            $table->string('work_type', 80);
            $table->string('period', 40)->nullable();
            $table->string('status', 24);
            $table->text('description');
            $table->string('evidence_reference', 255)->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
            $table->index(['engagement_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_work_updates');
    }
};
