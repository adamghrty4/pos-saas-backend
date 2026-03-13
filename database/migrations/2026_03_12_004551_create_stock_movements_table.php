<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('product_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null');

            $table->foreignId('sale_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null');

            $table->enum('type', [
                'sale',
                'manual_add',
                'manual_remove',
                'adjustment',
                'initial'
            ]);

            $table->integer('quantity');
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->string('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};