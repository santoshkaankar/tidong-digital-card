<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('billing_invoice_drafts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('outlet_id'); // maps to existing branches.id
            $table->unsignedBigInteger('created_by_user_id');
            $table->string('document_kind', 32); // tax_invoice, bill_invoice, proforma, stock_transfer
            $table->string('status', 16)->default('draft'); // NOT an issued invoice
            $table->string('currency', 3)->default('INR');
            $table->json('party_snapshot')->nullable();
            $table->json('tax_policy_snapshot')->nullable();
            $table->unsignedBigInteger('subtotal_paise')->default(0);
            $table->unsignedBigInteger('discount_paise')->default(0);
            $table->unsignedBigInteger('taxable_paise')->default(0);
            $table->unsignedBigInteger('tax_paise')->default(0);
            $table->unsignedBigInteger('grand_total_paise')->default(0);
            $table->unsignedInteger('revision')->default(1);
            $table->timestamps();
            $table->index(['business_id','outlet_id','status'], 'bid_scope_status_idx');
        });
        Schema::create('billing_invoice_draft_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('draft_id');
            $table->unsignedInteger('line_no');
            $table->string('description', 500);
            $table->string('hsn_sac', 16)->nullable();
            $table->unsignedBigInteger('quantity_milli');
            $table->unsignedBigInteger('unit_price_paise');
            $table->unsignedBigInteger('discount_paise')->default(0);
            $table->unsignedInteger('tax_rate_bps')->default(0);
            $table->unsignedBigInteger('taxable_paise');
            $table->unsignedBigInteger('tax_paise');
            $table->unsignedBigInteger('total_paise');
            $table->json('tax_snapshot')->nullable();
            $table->timestamps();
            $table->unique(['draft_id','line_no'], 'bid_line_no_uq');
            $table->foreign('draft_id','bid_line_draft_fk')->references('id')->on('billing_invoice_drafts')->restrictOnDelete();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('billing_invoice_draft_lines');
        Schema::dropIfExists('billing_invoice_drafts');
    }
};
