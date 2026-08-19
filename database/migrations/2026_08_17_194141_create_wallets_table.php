<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Wallets Table (Real Money & T-Coins balance ke liye)
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('real_balance', 12, 2)->default(0.00);
            $table->decimal('t_coins', 12, 2)->default(0.00);
            $table->timestamps();
        });

        // 2. Transactions Table (PhonePe / UPI / Real Money history ke liye)
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique();
            $table->decimal('amount', 12, 2);
            $table->enum('type', ['credit', 'debit']);
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->string('payment_gateway')->default('phonepe');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 3. T-Coin Transactions Table (Tokens ki history ke liye)
        Schema::create('t_coin_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('coins', 12, 2);
            $table->enum('type', ['credit', 'debit']);
            $table->string('description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_coin_transactions');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('wallets');
    }
};