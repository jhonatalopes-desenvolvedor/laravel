<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Barber;
use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;

class BarberDataSeeder extends Seeder
{
    use WithFaker;

    /**
     * Run the database seeds for a specific barber.
     *
     * @param Barber $barber
     * @param Collection<Service> $companyServices
     * @return void
     */
    public function run(Barber $barber, Collection $companyServices): void
    {
        $this->setUpFaker();

        // Endereço do barbeiro
        $barber->address()->create(
            \Database\Factories\BarberAddressFactory::new()->make()->toArray()
        );

        // Configurações de fila
        $barber->queueSettings()->create(
            \Database\Factories\BarberQueueSettingFactory::new()->openQueue()->make()->toArray()
        );

        // Horários recorrentes (Seg-X trabalhando, Sab/Dom não)
        for ($i = 0; $i <= 6; $i++) {
            if ($i >= 1 && $i <= 5) { // Monday to Friday
                $barber->recurringSchedules()->create(
                    \Database\Factories\BarberRecurringScheduleFactory::new()
                        ->forDay($i)
                        ->workingDay('09:00:00', '18:00:00')
                        ->make()
                        ->toArray()
                );
                // Adiciona uma pausa para almoço em dias de trabalho
                $barber->recurringBreaks()->create(
                    \Database\Factories\BarberRecurringBreakFactory::new()
                        ->forDay($i)
                        ->lunchBreak()
                        ->make()
                        ->toArray()
                );
            } else { // Saturday and Sunday
                $barber->recurringSchedules()->create(
                    \Database\Factories\BarberRecurringScheduleFactory::new()
                        ->forDay($i)
                        ->nonWorkingDay()
                        ->make()
                        ->toArray()
                );
            }
        }

        // Horários específicos (1-2 por barbeiro, alguns dias de folga, outros com horário diferente)
        $barber->specificSchedules()->createMany([
            \Database\Factories\BarberSpecificScheduleFactory::new()->forDate(Carbon::today()->addDays(rand(1, 14)))->nonWorkingDay()->make()->toArray(),
            \Database\Factories\BarberSpecificScheduleFactory::new()->forDate(Carbon::today()->addDays(rand(15, 30)))->workingDay('10:00:00', '16:00:00')->make()->toArray(),
        ]);

        // Pausas específicas (1-2 por barbeiro)
        $barber->specificBreaks()->createMany([
            \Database\Factories\BarberSpecificBreakFactory::new()->forPeriod(Carbon::now()->addDays(rand(1, 7))->setTime(14, 0), Carbon::now()->addDays(rand(1, 7))->setTime(15, 0))->make()->toArray(),
        ]);

        // Atribuir 3-5 serviços aleatórios ao barbeiro
        $barberServices = $companyServices->random(rand(3, count($companyServices)))->mapWithKeys(function ($service) {
            return [
                $service->id => [
                    'duration_minutes' => $this->faker->optional(0.5)->numberBetween(10, 100),
                    'price'            => $this->faker->optional(0.5)->randomFloat(2, 15, 250),
                ],
            ];
        })->toArray();
        $barber->services()->sync($barberServices);
    }
}
