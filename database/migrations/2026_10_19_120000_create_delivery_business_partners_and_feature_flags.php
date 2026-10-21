<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('delivery_business_partners')) {
            Schema::create('delivery_business_partners', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('business_id')->constrained('businesses')->restrictOnDelete();
                $table->foreignId('partner_id')->constrained('delivery_partners')->restrictOnDelete();
                $table->string('status', 24)->default('pending')->index();
                $table->foreignId('invited_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('verified_at')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();
                $table->unique(['business_id', 'partner_id']);
            });
        }
        if (! Schema::hasTable('vendor_delivery_permissions')) {
            Schema::create('vendor_delivery_permissions', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
                $table->foreignId('business_id')->constrained('businesses')->restrictOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained('branches')->restrictOnDelete();
                $table->string('permission_code', 100);
                $table->string('branch_scope_key', 48);
                $table->boolean('is_enabled')->default(false);
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->unique(['user_id', 'business_id', 'branch_scope_key', 'permission_code'], 'udv_scope_permission_unique');
                $table->index(['business_id', 'user_id', 'permission_code'], 'udv_bus_usr_perm_idx');
            });
        }
        if (! Schema::hasTable('platform_features')) {
            Schema::create('platform_features', function (Blueprint $table): void {
                $table->id();
                $table->string('code', 120)->unique();
                $table->string('name');
                $table->string('module_code', 80);
                $table->boolean('is_enabled')->default(false);
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        throw new \RuntimeException('Partner business approvals and feature history must be retained. Destructive rollback is blocked.');
    }
};
