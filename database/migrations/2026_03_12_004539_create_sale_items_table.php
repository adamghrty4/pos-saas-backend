<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('product_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('company_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Snapshot of product at time of sale
            $table->string('product_name');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};