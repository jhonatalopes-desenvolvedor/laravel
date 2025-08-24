<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\BarberSpecificBreak;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<BarberSpecificBreak>
 */
class BarberSpecificBreakFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = Carbon::instance($this->faker->dateTimeBetween('-1 month', '+1 month'));
        $end   = (clone $start)->addMinutes($this->faker->numberBetween(30, 180));

        return [
            'break_start_at' => $start,
            'break_end_at'   => $end,
            'reason'         => $this->faker->optional(0.7)->sentence(4),
        ];
    }

    /**
     * Configure a break for a specific date and time.
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return Factory
     */
    public function forPeriod(Carbon $start, Carbon $end): Factory
    {
        return $this->state(fn (array $attributes) => [
            'break_start_at' => $start,
            'break_end_at'   => $end,
        ]);
    }
}
