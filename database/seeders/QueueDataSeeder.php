<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Enums\QueueEntryStatus;
use App\Models\Barber;
use App\Models\Company;
use App\Models\CustomerProfile;
use App\Models\LiveQueue;
use App\Models\QueueEntry;
use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;

class QueueDataSeeder extends Seeder
{
    use WithFaker;

    /**
     * Run the database seeds for queue entries.
     *
     * @param Company $company
     * @param Collection<Barber> $barbers
     * @param Collection<Service> $services
     * @param Collection<CustomerProfile> $customerProfiles
     * @return void
     */
    public function run(
        Company $company,
        Collection $barbers,
        Collection $services,
        Collection $customerProfiles
    ): void {
        $this->setUpFaker();

        // Cerca de 50-100 entradas na fila por empresa (histórico e algumas ativas)
        $numQueueEntries = rand(50, 100);

        for ($i = 0; $i < $numQueueEntries; $i++) {
            $randomBarber          = $barbers->random();
            $randomCustomerProfile = $customerProfiles->random();
            $randomServices        = $services->random(rand(1, 3));

            $queueEntry = QueueEntry::factory()->for($company)->for($randomBarber)->for($randomCustomerProfile)->make()->toArray();

            // Crie o QueueEntry explicitamente para obter o modelo e anexar serviços
            $createdQueueEntry = QueueEntry::create($queueEntry);

            // Anexar serviços com dados pivot
            $pivotData              = [];
            $totalEstimatedDuration = 0;
            $totalActualDuration    = 0;
            $totalPriceCharged      = 0;

            foreach ($randomServices as $service) {
                $actualDuration = $this->faker->numberBetween((int) ($service->duration_minutes * 0.8), (int) ($service->duration_minutes * 1.2));
                $priceAtService = $this->faker->randomFloat(2, $service->price * 0.9, $service->price * 1.1);

                $pivotData[$service->id] = [
                    'actual_duration_minutes' => $createdQueueEntry->status === QueueEntryStatus::Finished ? $actualDuration : null,
                    'price_at_service'        => $priceAtService,
                ];
                $totalEstimatedDuration += $service->duration_minutes;
                $totalActualDuration += $actualDuration;
                $totalPriceCharged += $priceAtService;
            }
            $createdQueueEntry->services()->sync($pivotData);

            // Atualiza o total_amount_charged se for finalizado
            if ($createdQueueEntry->status === QueueEntryStatus::Finished) {
                $createdQueueEntry->total_amount_charged = $totalPriceCharged;
                $createdQueueEntry->save();
            }

            // Crie LiveQueue para entradas que ainda estão ativas (status Entered, BeingCalled, InService)
            if ($createdQueueEntry->status === QueueEntryStatus::Entered) {
                // Determinar a próxima ordem na fila para este barbeiro
                $maxOrder = LiveQueue::where('barber_id', $randomBarber->id)
                    ->where('company_id', $company->id)
                    ->max('queue_order');
                $newOrder = ($maxOrder ?? 0) + 1;

                LiveQueue::factory()->for($createdQueueEntry)->for($randomBarber)->for($company)->for($randomCustomerProfile)->inQueue()->create([
                    'estimated_service_duration_minutes' => $totalEstimatedDuration,
                    'estimated_wait_time_minutes'        => $this->faker->numberBetween(0, 60),
                    'queue_order'                        => $newOrder,
                ]);
            }
        }
    }
}
