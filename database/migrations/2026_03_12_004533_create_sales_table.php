<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Reference number on receipt
            $table->string('reference')->unique();

            // Amounts
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Payment
            $table->enum('payment_method', [
                'cash',
                'card',
                'mixed'
            ])->default('cash');
            $table->decimal('cash_received', 10, 2)->nullable();
            $table->decimal('change_given', 10, 2)->nullable();

            // Status
            $table->enum('status', [
                'completed',
                'refunded',
                'cancelled'
            ])->default('completed');

            $table->string('note')->nullable();
            $table->string('table_number')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};


