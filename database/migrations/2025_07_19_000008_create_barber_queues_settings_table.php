<?php

declare(strict_types = 1);

use App\Enums\BarberQueueState;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('barber_queues_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('barber_id')->constrained('barbers')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedTinyInteger('queue_state')->default(BarberQueueState::Closed->value);
            $table->unsignedInteger('max_capacity')->default(10);
            $table->timestamps();
            $table->unique('barber_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barber_queues_settings');
    }
};
