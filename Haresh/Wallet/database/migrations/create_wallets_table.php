<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: CreateWalletsTable
 *
 * Wallets are standalone and not directly tied to users in schema.
 * Type can represent 'points', 'cash', etc.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'cash', 'points', etc.
            $table->decimal('balance', 12, 2)->default(0);
            $table->timestamps();

            $table->unique('type'); // Optional: one wallet per type globally
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallets');
    }
};
