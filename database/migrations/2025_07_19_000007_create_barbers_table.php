<?php

declare(strict_types = 1);

use App\Enums\BarberStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('barbers', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('is_active')->default(true);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('cpf', 14)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('email');
            $table->string('password');
            $table->unsignedTinyInteger('current_status')->default(BarberStatus::Unavailable->value);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'email']);
            $table->unique(['company_id', 'cpf']);
        });

        Schema::create('barber_password_reset_tokens', function (Blueprint $table): void {
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
        Schema::dropIfExists('barbers');
        Schema::dropIfExists('barber_password_reset_tokens');
    }
};
