<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\BarberRecurringBreak;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BarberRecurringBreak>
 */
class BarberRecurringBreakFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = $this->faker->time('H:i', '12:00');
        $endTime   = $this->faker->time('H:i', '14:00');

        // Ensure end time is after start time
        if ($startTime > $endTime) {
            [$startTime, $endTime] = [$endTime, $startTime];
        }

        return [
            'day_of_week'      => $this->faker->numberBetween(0, 6),
            'break_start_time' => $startTime,
            'break_end_time'   => $endTime,
            'reason'           => $this->faker->optional(0.5)->sentence(3),
        ];
    }

    /**
     * Configure a specific day of the week.
     *
     * @param int $dayOfWeek
     * @return Factory
     */
    public function forDay(int $dayOfWeek): Factory
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => $dayOfWeek,
        ]);
    }

    /**
     * Define a standard lunch break.
     *
     * @return Factory
     */
    public function lunchBreak(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'break_start_time' => '12:00:00',
            'break_end_time'   => '13:00:00',
            'reason'           => 'Almoço',
        ]);
    }
}
