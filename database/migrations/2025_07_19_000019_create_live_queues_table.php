<?php

declare(strict_types = 1);

use App\Enums\CustomerArrivalStatus;
use App\Enums\LiveQueueStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('live_queues', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('queue_entry_id')->constrained('queue_entries')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('barber_id')->constrained('barbers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('customer_profile_id')->constrained('customer_profiles')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedTinyInteger('current_status')->default(LiveQueueStatus::InQueue->value);
            $table->unsignedTinyInteger('customer_arrival_status')->default(CustomerArrivalStatus::OffSite->value);
            $table->integer('estimated_service_duration_minutes')->nullable();
            $table->integer('estimated_wait_time_minutes')->nullable();
            $table->bigInteger('queue_order')->index()->comment('🔑 campo de ordenação com gaps');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_queues');
    }
};
