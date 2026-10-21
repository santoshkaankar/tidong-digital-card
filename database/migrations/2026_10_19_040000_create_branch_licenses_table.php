<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('branch_licenses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->string('license_type', 40);
            $table->string('license_number', 100);
            $table->string('registered_name')->nullable();
            $table->string('issuing_authority')->nullable();
            $table->date('issued_on')->nullable();
            $table->date('expires_on')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            // A GSTIN may be shared by branches, but don't duplicate a record within one branch.
            $table->unique(['branch_id', 'license_type', 'license_number'], 'branch_license_identity_unique');
            $table->index(['branch_id', 'license_type'], 'branch_license_type_index');
            $table->index('expires_on');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_licenses');
    }
};
