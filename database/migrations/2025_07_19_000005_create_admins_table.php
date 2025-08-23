<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->uuid('uuid')->unique();
            $table->string('language_code', 10)->default('pt-BR');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('password');
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'email']);
        });

        Schema::create('admin_password_reset_tokens', function (Blueprint $table): void {
            $table->id();
            $table->string('email');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->string('token');
            $table->timestamp('created_at')->nullable();
            $table->unique(['email', 'company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
        Schema::dropIfExists('admin_password_reset_tokens');
    }
};
