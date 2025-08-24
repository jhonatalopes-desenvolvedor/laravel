<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Enums\CustomerArrivalStatus;
use App\Enums\LiveQueueStatus;
use App\Models\LiveQueue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LiveQueue>
 */
class LiveQueueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'current_status'                     => $this->faker->randomElement(LiveQueueStatus::cases()),
            'customer_arrival_status'            => $this->faker->randomElement(CustomerArrivalStatus::cases()),
            'estimated_service_duration_minutes' => $this->faker->numberBetween(15, 120),
            'estimated_wait_time_minutes'        => $this->faker->numberBetween(0, 60),
            'queue_order'                        => $this->faker->unique()->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the entry is in queue.
     *
     * @return Factory
     */
    public function inQueue(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'current_status' => LiveQueueStatus::InQueue,
        ]);
    }

    /**
     * Indicate that the entry is being called.
     *
     * @return Factory
     */
    public function beingCalled(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'current_status' => LiveQueueStatus::BeingCalled,
        ]);
    }

    /**
     * Indicate that the entry is in service.
     *
     * @return Factory
     */
    public function inService(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'current_status' => LiveQueueStatus::InService,
        ]);
    }

    /**
     * Indicate that the customer is on site.
     *
     * @return Factory
     */
    public function onSite(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'customer_arrival_status' => CustomerArrivalStatus::OnSite,
        ]);
    }
}
