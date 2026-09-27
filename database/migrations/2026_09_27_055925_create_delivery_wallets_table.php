<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Delivery Boy Wallet Summary
        Schema::create('delivery_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('earning_balance', 10, 2)->default(0.00); // Delivery Boy ka main earning balance
            $table->decimal('cash_in_hand', 10, 2)->default(0.00);    // COD cash collected from customer
            $table->timestamps();
        });

        // 2. Delivery Boy Wallet Passbook / Transactions
        Schema::create('delivery_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->enum('type', ['earning_credit', 'cod_cash_collected', 'admin_payout', 'penalty']);
            $table->decimal('amount', 10, 2);
            $table->string('description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_wallet_transactions');
        Schema::dropIfExists('delivery_wallets');
    }
};