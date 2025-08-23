<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->string('phone_number', 20);
            $table->timestamps();
            $table->unique(['company_id', 'phone_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
