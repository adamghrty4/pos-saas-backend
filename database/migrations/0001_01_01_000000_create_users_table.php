<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('cascade');

            // Basic info
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();

            // PIN for waiters
            $table->string('pin_code', 6)->nullable();
            $table->boolean('pin_active')->default(true);

            // Role
            $table->enum('role', [
                'super_admin',
                'owner',
                'manager',
                'waiter'
            ])->default('waiter');

            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
