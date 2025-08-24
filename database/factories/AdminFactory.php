<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Admin>
 */
class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid'          => Str::uuid(),
            'first_name'    => $this->faker->firstName(),
            'last_name'     => $this->faker->lastName(),
            'email'         => $this->faker->unique()->safeEmail(),
            'password'      => Hash::make('password'),
            'language_code' => $this->faker->randomElement(['pt-BR', 'en-US']),
            'last_login_at' => $this->faker->optional(0.7)->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Summary of superAdmin
     *
     * @return Factory
     */
    public function superAdmin(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'first_name' => 'Super',
            'last_name'  => 'Admin',
            'email'      => 'super@admin.com',
        ]);
    }
}
