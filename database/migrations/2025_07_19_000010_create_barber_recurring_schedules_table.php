<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('barber_recurring_schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('barber_id')->constrained('barbers')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_working_day')->default(true);
            $table->timestamps();
            $table->unique(['barber_id', 'day_of_week'], 'barber_recurring_schedules_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barber_recurring_schedules');
    }
};
