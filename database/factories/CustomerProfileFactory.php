<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Enums\CustomerArrivalStatus;
use App\Enums\CustomerRelationshipType;
use App\Models\CustomerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomerProfile>
 */
class CustomerProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'profile_name'      => $this->faker->name(),
            'relationship_type' => $this->faker->randomElement(CustomerRelationshipType::cases()),
            'arrival_status'    => $this->faker->randomElement(CustomerArrivalStatus::cases()),
        ];
    }

    /**
     * Indicate that the profile is the account holder (Self).
     *
     * @param string|null $name
     * @return Factory
     */
    public function selfProfile(?string $name = null): Factory
    {
        return $this->state(fn (array $attributes) => [
            'profile_name'      => $name ?? $this->faker->name(),
            'relationship_type' => CustomerRelationshipType::Self,
        ]);
    }
}
