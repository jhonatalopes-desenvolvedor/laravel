<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('queue_entry_service', function (Blueprint $table): void {
            $table->foreignId('queue_entry_id')->constrained('queue_entries')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('actual_duration_minutes')->nullable();
            $table->decimal('price_at_service', 8, 2)->nullable();
            $table->primary(['queue_entry_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_entry_service');
    }
};
