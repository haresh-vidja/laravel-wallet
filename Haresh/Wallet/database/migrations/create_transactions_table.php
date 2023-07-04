<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: CreateTransactionsTable
 * 
 * Adds reference UUID, metadata JSON, and status (pending/approved/rejected).
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->enum('type', ['credit', 'debit']);
            $table->string('description')->nullable();
            $table->json('meta')->nullable(); // Extra structured info (order ID, etc.)
            $table->uuid('reference')->unique(); // Unique transaction reference
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->timestamps();
            $table->softDeletes(); // Enable soft deletes for audit trails
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
