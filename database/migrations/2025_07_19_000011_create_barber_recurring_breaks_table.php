<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('barber_recurring_breaks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('barber_id')->constrained('barbers')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedTinyInteger('day_of_week');
            $table->time('break_start_time');
            $table->time('break_end_time');
            $table->string('reason', 255)->nullable();
            $table->timestamps();
            $table->unique(['barber_id', 'day_of_week', 'break_start_time', 'break_end_time'], 'barber_recurring_breaks_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barber_recurring_breaks');
    }
};
