<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Company;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;

class CompanyDataSeeder extends Seeder
{
    use WithFaker;

    /**
     * Run the database seeds for a specific company.
     *
     * @param Company $company
     */
    public function run(Company $company): void
    {
        $this->setUpFaker();

        // Endereço da empresa
        $company->address()->create(
            \Database\Factories\CompanyAddressFactory::new()->make()->toArray()
        );

        // Configurações de API (algumas com dados, outras nulas)
        if ($this->faker->boolean(70)) {
            $company->apiSettings()->create(
                \Database\Factories\CompanyApiSettingFactory::new()->make()->toArray()
            );
        } else {
            $company->apiSettings()->create(
                \Database\Factories\CompanyApiSettingFactory::new()->nullSettings()->make()->toArray()
            );
        }

        // Horários de funcionamento (Seg-Sex abertos, Sab/Dom fechados)
        for ($i = 0; $i <= 6; $i++) {
            if ($i >= 1 && $i <= 5) { // Monday to Friday
                $company->operatingHours()->create(
                    \Database\Factories\CompanyOperatingHourFactory::new()
                        ->forDay($i)
                        ->workingDay('09:00:00', '18:00:00')
                        ->make()
                        ->toArray()
                );
            } else { // Saturday and Sunday
                $company->operatingHours()->create(
                    \Database\Factories\CompanyOperatingHourFactory::new()
                        ->forDay($i)
                        ->closedDay()
                        ->make()
                        ->toArray()
                );
            }
        }

        // Feriados (2 por empresa, um fechado total e outro com horário reduzido)
        $company->holidays()->createMany([
            \Database\Factories\CompanyHolidayFactory::new()->fullDayClosed()->make()->toArray(),
            \Database\Factories\CompanyHolidayFactory::new()->customHours('09:00:00', '13:00:00')->make()->toArray(),
        ]);

        // Admins (1 super admin por empresa e mais 2-3 normais)
        Admin::factory()->for($company)->create([
            'first_name' => 'Admin',
            'last_name'  => 'Test',
            'email'      => 'admin@' . $company->domain,
            'password'   => Hash::make('password'),
        ]);
        Admin::factory(rand(2, 3))->for($company)->create();

        // Serviços (5-10 por empresa)
        $services = Service::factory(rand(5, 10))->for($company)->active()->create();

        // Salva os serviços na company para uso posterior
        $company->services = $services;
    }
}
