<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create user_wallets pivot table with wallet_type
 *
 * - Links users to wallets
 * - Each user can have multiple wallets
 * - Each user can have only one wallet per type (enforced via unique constraint)
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_wallets', function (Blueprint $table) {
            $table->id();

            // Foreign key to users table
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Foreign key to wallets table
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');

            // Wallet type per user (e.g., 'cash', 'points')
            $table->string('wallet_type');

            $table->timestamps();

            // One user can have only one wallet per type
            $table->unique(['user_id', 'wallet_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_wallets');
    }
};