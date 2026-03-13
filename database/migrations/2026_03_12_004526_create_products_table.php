<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null');

            // Product info
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->string('barcode')->nullable();

            // Pricing
            $table->decimal('price', 10, 2);
            $table->decimal('cost_price', 10, 2)->nullable();

            // Stock
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_alert')->default(5);
            $table->boolean('track_stock')->default(true);

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_available')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};