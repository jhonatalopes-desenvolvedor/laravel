<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerProfile;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;

class CustomerDataSeeder extends Seeder
{
    use WithFaker;

    /**
     * Run the database seeds for a specific customer.
     *
     * @param Customer $customer
     */
    public function run(Customer $customer): void
    {
        $this->setUpFaker();

        // Perfil Titular da Conta
        $customer->profiles()->create(
            \Database\Factories\CustomerProfileFactory::new()->selfProfile($this->faker->name())->make()->toArray()
        );
        // Mais 1-3 perfis adicionais (Filho, Amigo, etc.)
        CustomerProfile::factory(rand(0, 3))->for($customer)->create();
    }
}
