<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\CompanyAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanyAddress>
 */
class CompanyAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'postal_code'     => $this->faker->postcode(),
            'street'          => $this->faker->streetName(),
            'number'          => $this->faker->buildingNumber(),
            'complement'      => $this->faker->optional(0.3)->secondaryAddress(),
            'neighborhood'    => $this->faker->citySuffix(),
            'city'            => $this->faker->city(),
            'state'           => $this->faker->stateAbbr(),
            'country'         => $this->faker->country(),
            'google_maps_url' => $this->faker->optional(0.5)->url(),
        ];
    }
}
