<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Enums\QueueEntryStatus;
use App\Models\QueueEntry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<QueueEntry>
 */
class QueueEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $enteredAt = Carbon::instance($this->faker->dateTimeBetween('-6 months', 'now'));
        $status    = $this->faker->randomElement(QueueEntryStatus::cases());

        $startedAt          = null;
        $finishedAt         = null;
        $totalAmountCharged = null;

        if ($status === QueueEntryStatus::Finished) {
            $startedAt          = (clone $enteredAt)->addMinutes($this->faker->numberBetween(5, 30));
            $finishedAt         = (clone $startedAt)->addMinutes($this->faker->numberBetween(30, 120));
            $totalAmountCharged = $this->faker->randomFloat(2, 50, 500);
        } elseif ($status === QueueEntryStatus::Canceled || $status === QueueEntryStatus::NoShow) {
        } else {
            $startedAt = $this->faker->optional(0.3)->dateTimeBetween($enteredAt, 'now');
        }

        return [
            'status'               => $status,
            'entered_at'           => $enteredAt,
            'started_at'           => $startedAt,
            'finished_at'          => $finishedAt,
            'total_amount_charged' => $totalAmountCharged,
        ];
    }

    /**
     * Indicate that the queue entry is finished.
     *
     * @return Factory
     */
    public function finished(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status'               => QueueEntryStatus::Finished,
            'entered_at'           => Carbon::instance($this->faker->dateTimeBetween('-6 months', '-1 day')),
            'started_at'           => fn (array $attributes) => Carbon::instance($attributes['entered_at'])->addMinutes($this->faker->numberBetween(5, 30)),
            'finished_at'          => fn (array $attributes) => Carbon::instance($attributes['started_at'])->addMinutes($this->faker->numberBetween(30, 120)),
            'total_amount_charged' => $this->faker->randomFloat(2, 50, 500),
        ]);
    }

    /**
     * Indicate that the queue entry is canceled.
     *
     * @return Factory
     */
    public function canceled(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status'               => QueueEntryStatus::Canceled,
            'entered_at'           => Carbon::instance($this->faker->dateTimeBetween('-6 months', 'now')),
            'started_at'           => null,
            'finished_at'          => null,
            'total_amount_charged' => null,
        ]);
    }

    /**
     * Indicate that the queue entry is a no-show.
     *
     * @return Factory
     */
    public function noShow(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status'               => QueueEntryStatus::NoShow,
            'entered_at'           => Carbon::instance($this->faker->dateTimeBetween('-6 months', 'now')),
            'started_at'           => null,
            'finished_at'          => null,
            'total_amount_charged' => null,
        ]);
    }

    /**
     * Indicate that the queue entry is currently active (Entered, BeingCalled, InService).
     *
     * @return Factory
     */
    public function activeEntry(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status'               => QueueEntryStatus::Entered,
            'entered_at'           => Carbon::instance($this->faker->dateTimeBetween('-1 hour', 'now')),
            'started_at'           => null,
            'finished_at'          => null,
            'total_amount_charged' => null,
        ]);
    }
}
