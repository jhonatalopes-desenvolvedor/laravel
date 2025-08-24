<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\BarberSpecificSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<BarberSpecificSchedule>
 */
class BarberSpecificScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date         = $this->faker->unique()->dateTimeBetween('-1 month', '+3 months')->format('Y-m-d');
        $isWorkingDay = $this->faker->boolean(70);
        $startTime    = $isWorkingDay ? $this->faker->time('H:i', '08:00') : null;
        $endTime      = $isWorkingDay ? $this->faker->time('H:i', '17:00') : null;

        return [
            'date'           => $date,
            'start_time'     => $startTime,
            'end_time'       => $endTime,
            'is_working_day' => $isWorkingDay,
            'reason'         => $this->faker->optional(0.5)->sentence(3),
        ];
    }

    /**
     * Indicate that the specific schedule is a working day.
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
     * Indicate that the specific schedule is a non-working day.
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

    /**
     * Configure a specific date.
     *
     * @param string|Carbon $date
     * @return Factory
     */
    public function forDate(string|Carbon $date): Factory
    {
        return $this->state(fn (array $attributes) => [
            'date' => $date,
        ]);
    }
}
