<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Enums\BarberStatus;
use App\Models\Barber;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Barber>
 */
class BarberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid'           => Str::uuid(),
            'is_active'      => $this->faker->boolean(90),
            'first_name'     => $this->faker->firstName(),
            'last_name'      => $this->faker->lastName(),
            'cpf'            => $this->faker->optional(0.5)->numerify('###.###.###-##'),
            'phone_number'   => $this->faker->optional(0.7)->phoneNumber(),
            'email'          => $this->faker->unique()->safeEmail(),
            'password'       => Hash::make('password'),
            'current_status' => $this->faker->randomElement(BarberStatus::cases()),
            'last_login_at'  => $this->faker->optional(0.7)->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the barber is active.
     *
     * @return Factory
     */
    public function active(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'is_active'      => true,
            'current_status' => BarberStatus::Available,
        ]);
    }

    /**
     * Indicate that the barber is unavailable.
     *
     * @return Factory
     */
    public function unavailable(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'is_active'      => false,
            'current_status' => BarberStatus::Unavailable,
        ]);
    }
}
