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
        Schema::create('wallets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('currency', ['NGN', 'USD', 'GBP', 'EUR'])->default('NGN');
            $table->uuid('owner_id');
            $table->string('owner_type');
            $table->boolean('is_active')->default(true);
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['owner_id', 'currency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
