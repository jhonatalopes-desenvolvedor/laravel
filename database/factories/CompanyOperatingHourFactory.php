<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\CompanyOperatingHour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanyOperatingHour>
 */
class CompanyOperatingHourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isClosed  = $this->faker->boolean(10);
        $openTime  = $isClosed ? null : $this->faker->time('H:i', '09:00');
        $closeTime = $isClosed ? null : $this->faker->time('H:i', '18:00');

        return [
            'day_of_week' => $this->faker->numberBetween(0, 6),
            'open_time'   => $openTime,
            'close_time'  => $closeTime,
            'is_closed'   => $isClosed,
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
     * @param string $openTime
     * @param string $closeTime
     * @return Factory
     */
    public function workingDay(string $openTime = '09:00:00', string $closeTime = '18:00:00'): Factory
    {
        return $this->state(fn (array $attributes) => [
            'open_time'  => $openTime,
            'close_time' => $closeTime,
            'is_closed'  => false,
        ]);
    }

    /**
     * Indicate that the day is closed.
     *
     * @return Factory
     */
    public function closedDay(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'open_time'  => null,
            'close_time' => null,
            'is_closed'  => true,
        ]);
    }
}
