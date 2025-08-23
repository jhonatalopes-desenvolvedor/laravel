<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('barber_specific_breaks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('barber_id')->constrained('barbers')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamp('break_start_at');
            $table->timestamp('break_end_at')->nullable();
            $table->string('reason', 255)->nullable();
            $table->timestamps();
            $table->index('break_start_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barber_specific_breaks');
    }
};
