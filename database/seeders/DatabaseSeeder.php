<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Enums\BarberStatus;
use App\Models\Admin;
use App\Models\Barber;
use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerProfile;
use App\Models\LiveQueue;
use App\Models\QueueEntry;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithFaker;

    public function run(): void
    {
        $this->setUpFaker();

        $companies = Company::factory(3)->active()->create();

        foreach ($companies as $company) {
            $this->callWith(CompanyDataSeeder::class, ['company' => $company]);

            $barbers = Barber::factory(rand(3, 5))->for($company)->active()->create();

            foreach ($barbers as $barber) {
                $this->callWith(BarberDataSeeder::class, ['barber' => $barber, 'companyServices' => $company->services]);
            }

            $customers = Customer::factory(rand(10, 20))->for($company)->create();

            foreach ($customers as $customer) {
                $this->callWith(CustomerDataSeeder::class, ['customer' => $customer]);
            }

            $allCustomerProfiles = CustomerProfile::whereHas('customer', fn ($q) => $q->where('company_id', $company->id))->get();
            $this->callWith(QueueDataSeeder::class, [
                'company'          => $company,
                'barbers'          => $barbers,
                'services'         => $company->services,
                'customerProfiles' => $allCustomerProfiles,
            ]);
        }

        $companyTestFila = Company::factory()->active()->create([
            'name'  => 'Barbearia Teste Fila',
            'email' => 'fila@example.com',
        ]);

        $this->callWith(CompanyDataSeeder::class, ['company' => $companyTestFila]);

        Admin::factory()->for($companyTestFila)->create([
            'first_name' => 'AdminFila',
            'last_name'  => 'Test',
            'email'      => 'admin.fila@' . $companyTestFila->domain,
            'password'   => Hash::make('password'),
        ]);

        $testBarber = Barber::factory()->for($companyTestFila)->active()->create([
            'first_name'     => 'Barbeiro',
            'last_name'      => 'Fila',
            'email'          => 'barber.fila@' . $companyTestFila->domain,
            'current_status' => BarberStatus::Available,
            'password'       => Hash::make('password'),
        ]);

        $this->callWith(BarberDataSeeder::class, ['barber' => $testBarber, 'companyServices' => $companyTestFila->services]);

        $customersForTest = Customer::factory(10)->for($companyTestFila)->create();

        foreach ($customersForTest as $customer) {
            $this->callWith(CustomerDataSeeder::class, ['customer' => $customer]);
        }

        $testCustomerProfiles = CustomerProfile::whereHas('customer', fn ($q) => $q->where('company_id', $companyTestFila->id))->get();
        $queueOrder           = 1;

        foreach ($testCustomerProfiles->take(5) as $profile) {
            $randomServices    = $companyTestFila->services->random(rand(1, 2));
            $estimatedDuration = $randomServices->sum('duration_minutes');

            $queueEntry = QueueEntry::factory()->for($companyTestFila)->for($testBarber)->for($profile)->activeEntry()->create();
            $pivotData  = $randomServices->mapWithKeys(fn ($s) => [$s->id => ['price_at_service' => $s->price]])->toArray();
            $queueEntry->services()->sync($pivotData);

            LiveQueue::factory()->for($queueEntry)->for($testBarber)->for($companyTestFila)->for($profile)->inQueue()->create([
                'estimated_service_duration_minutes' => $estimatedDuration,
                'estimated_wait_time_minutes'        => $this->faker->numberBetween(0, 60),
                'queue_order'                        => $queueOrder++,
            ]);
        }
    }
}
