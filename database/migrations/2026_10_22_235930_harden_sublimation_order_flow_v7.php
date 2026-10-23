<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sublimation_orders', function (Blueprint $table): void {
            // Nullable for historical V1-V6 jobs; each V7 request must supply a UUID.
            $table->uuid('request_token')->nullable()->unique('subl_order_request_token_unique');
            $table->unsignedBigInteger('request_actor_user_id')->nullable();
            $table->char('request_payload_hash', 64)->nullable();
            $table->unsignedInteger('design_revision')->default(1);
            $table->unsignedInteger('approved_revision')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sublimation_orders', function (Blueprint $table): void {
            $table->dropUnique('subl_order_request_token_unique');
            $table->dropColumn(['request_token', 'request_actor_user_id', 'request_payload_hash', 'design_revision', 'approved_revision']);
        });
    }
};
