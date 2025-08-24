<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\CompanyHoliday;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanyHoliday>
 */
class CompanyHolidayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date      = $this->faker->unique()->dateTimeBetween('+1 month', '+1 year')->format('Y-m-d');
        $isClosed  = $this->faker->boolean(80);
        $openTime  = $isClosed ? null : $this->faker->time('H:i', '09:00');
        $closeTime = $isClosed ? null : $this->faker->time('H:i', '13:00');

        return [
            'date'        => $date,
            'description' => $this->faker->sentence(3),
            'is_closed'   => $isClosed,
            'open_time'   => $openTime,
            'close_time'  => $closeTime,
        ];
    }

    /**
     * Indicate that the holiday is a full closed day.
     *
     * @return Factory
     */
    public function fullDayClosed(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'is_closed'  => true,
            'open_time'  => null,
            'close_time' => null,
        ]);
    }

    /**
     * Indicate that the holiday has custom hours.
     *
     * @param string $openTime
     * @param string $closeTime
     * @return Factory
     */
    public function customHours(string $openTime, string $closeTime): Factory
    {
        return $this->state(fn (array $attributes) => [
            'is_closed'  => false,
            'open_time'  => $openTime,
            'close_time' => $closeTime,
        ]);
    }
}
