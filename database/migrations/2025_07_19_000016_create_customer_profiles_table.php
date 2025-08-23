<?php

declare(strict_types = 1);

use App\Enums\CustomerArrivalStatus;
use App\Enums\CustomerRelationshipType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('customer_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade')->onUpdate('cascade');
            $table->string('profile_name', 100);
            $table->unsignedTinyInteger('relationship_type')->default(CustomerRelationshipType::Self->value);
            $table->unsignedTinyInteger('arrival_status')->default(CustomerArrivalStatus::OffSite->value);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};
