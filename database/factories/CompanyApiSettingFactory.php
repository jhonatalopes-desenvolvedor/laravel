<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\CompanyApiSetting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CompanyApiSetting>
 */
class CompanyApiSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'whatsapp_token_access'    => Str::random(60),
            'whatsapp_token_verify'    => Str::random(20),
            'whatsapp_phone_number_id' => $this->faker->numerify('############'),
        ];
    }

    /**
     * Indicate that the API settings are null.
     *
     * @return Factory
     */
    public function nullSettings(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'whatsapp_token_access'    => null,
            'whatsapp_token_verify'    => null,
            'whatsapp_phone_number_id' => null,
        ]);
    }
}
