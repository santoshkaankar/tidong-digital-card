<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Vendor Wallets Table
        Schema::create('vendor_wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id')->unique(); // Kis vendor/business ka wallet hai
            $table->decimal('bonus_balance', 10, 2)->default(200.00); // Non-withdrawable signup bonus (default ₹200)
            $table->decimal('sales_balance', 10, 2)->default(0.00);    // Withdrawable customer sales amount
            $table->timestamps();
        });

        // 2. Wallet Transactions / Ledger Table (Credit/Debit History)
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->enum('wallet_type', ['bonus', 'sales']); // Kis wallet se transaction hui
            $table->enum('type', ['credit', 'debit']);       // Paisa aaya ya kata
            $table->decimal('amount', 10, 2);                // Transaction amount
            $table->string('description');                   // Jaise: 'Signup Bonus Credited', 'Daily Platform Fee', 'Order Sale Payout', 'Bank Withdrawal'
            $table->unsignedBigInteger('order_id')->nullable(); // Agar order se related ho toh
            $table->string('status')->default('success');    // success, pending, failed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('vendor_wallets');
    }
};