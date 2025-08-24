<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Enums\BarberQueueState;
use App\Models\BarberQueueSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BarberQueueSetting>
 */
class BarberQueueSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'queue_state'  => $this->faker->randomElement(BarberQueueState::cases()),
            'max_capacity' => $this->faker->numberBetween(5, 20),
        ];
    }

    /**
     * Indicate that the queue is open.
     *
     * @return Factory
     */
    public function openQueue(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'queue_state' => BarberQueueState::Open,
        ]);
    }

    /**
     * Indicate that the queue is closed.
     *
     * @return Factory
     */
    public function closedQueue(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'queue_state' => BarberQueueState::Closed,
        ]);
    }
}
