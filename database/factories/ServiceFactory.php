<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'is_active'        => $this->faker->boolean(90),
            'name'             => $this->faker->unique()->word() . ' ' . $this->faker->randomElement(['Cut', 'Shave', 'Treatment', 'Massage']),
            'description'      => $this->faker->optional(0.7)->sentence(),
            'duration_minutes' => $this->faker->numberBetween(15, 90),
            'price'            => $this->faker->randomFloat(2, 20, 200),
        ];
    }

    /**
     * Indicate that the service is active.
     *
     * @return Factory
     */
    public function active(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}
