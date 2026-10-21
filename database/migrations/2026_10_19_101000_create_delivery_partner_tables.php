<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('delivery_partners', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->restrictOnDelete();
            $table->string('vehicle_type', 30);
            $table->string('phone', 30)->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('delivery_jobs', function (Blueprint $table): void {
            $table->id();
            $table->string('reference', 50)->unique();
            $table->foreignId('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('pos_order_id')->nullable()->constrained('pos_orders')->restrictOnDelete();
            $table->foreignId('partner_id')->constrained('delivery_partners')->restrictOnDelete();
            $table->string('status', 25)->default('assigned')->index();
            $table->string('recipient_name', 160);
            $table->string('recipient_phone', 30);
            $table->text('dropoff_address');
            $table->string('delivery_note', 500)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->index(['partner_id', 'status']);
            $table->index(['branch_id', 'status']);
        });

        Schema::create('delivery_job_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('delivery_job_id')->constrained('delivery_jobs')->restrictOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50);
            $table->string('old_status', 25)->nullable();
            $table->string('new_status', 25);
            $table->json('details')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['delivery_job_id', 'created_at']);
        });
    }

    public function down(): void
    {
        throw new \RuntimeException('Delivery records and assignment history must be retained. Automatic rollback is disabled.');
    }
};
