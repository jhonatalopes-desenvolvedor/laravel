<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Enums\CompanyOperationalStatus;
use App\Enums\CompanySaaSStatus;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'               => $this->faker->company(),
            'domain'             => $this->faker->unique()->domainName(),
            'email'              => $this->faker->unique()->companyEmail(),
            'language_code'      => $this->faker->randomElement(['pt-BR', 'en-US', 'es-ES']),
            'timezone'           => $this->faker->randomElement(['America/Sao_Paulo', 'America/New_York', 'Europe/London']),
            'saas_status'        => $this->faker->randomElement(CompanySaaSStatus::cases()),
            'operational_status' => $this->faker->randomElement(CompanyOperationalStatus::cases()),
        ];
    }

    /**
     * Indicate that the company is active.
     *
     * @return Factory
     */
    public function active(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'saas_status'        => CompanySaaSStatus::Active,
            'operational_status' => CompanyOperationalStatus::Open,
        ]);
    }
}
