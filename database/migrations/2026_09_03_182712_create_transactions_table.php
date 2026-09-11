<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('transactions_user_id_foreign');
            $table->string('transaction_id')->unique();
            $table->decimal('amount', 12);
            $table->enum('type', ['credit', 'debit']);
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->string('payment_gateway')->default('phonepe');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
