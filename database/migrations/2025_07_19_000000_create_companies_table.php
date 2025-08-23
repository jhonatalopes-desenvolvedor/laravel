<?php

declare(strict_types = 1);

use App\Enums\CompanyOperationalStatus;
use App\Enums\CompanySaaSStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table): void {
            $table->id();
            $table->unsignedTinyInteger('saas_status')->default(CompanySaaSStatus::Active->value);
            $table->unsignedTinyInteger('operational_status')->default(CompanyOperationalStatus::Open->value);
            $table->string('name');
            $table->string('domain')->unique();
            $table->string('email')->unique();
            $table->string('language_code', 10)->default('pt-BR');
            $table->string('timezone')->default('America/Sao_Paulo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
