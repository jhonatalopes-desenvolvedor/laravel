<?php

declare(strict_types = 1);

use App\Enums\QueueEntryStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('queue_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('barber_id')->constrained('barbers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('customer_profile_id')->constrained('customer_profiles')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedTinyInteger('status')->default(QueueEntryStatus::Entered->value);
            $table->timestamp('entered_at')->useCurrent();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->decimal('total_amount_charged', 8, 2)->nullable();
            $table->timestamps();
            $table->index(['company_id', 'barber_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_entries');
    }
};
