<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\BarberRecurringSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BarberRecurringSchedule>
 */
class BarberRecurringScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'day_of_week'    => $this->faker->numberBetween(0, 6),
            'start_time'     => null,
            'end_time'       => null,
            'is_working_day' => true,
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
     * Indicate that the day is a working day.
     *
     * @param string $startTime
     * @param string $endTime
     * @return Factory
     */
    public function workingDay(string $startTime = '09:00:00', string $endTime = '18:00:00'): Factory
    {
        return $this->state(fn (array $attributes) => [
            'start_time'     => $startTime,
            'end_time'       => $endTime,
            'is_working_day' => true,
        ]);
    }

    /**
     * Indicate that the day is a non-working day.
     *
     * @return Factory
     */
    public function nonWorkingDay(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'start_time'     => null,
            'end_time'       => null,
            'is_working_day' => false,
        ]);
    }
}
