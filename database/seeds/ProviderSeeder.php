<?php

use App\Repositories\ProviderRepository;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{

    public function __construct(ProviderRepository $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $p = $this->provider->create(['name' => 'Correos de México']);
        $this->provider->addServiceType($p, ['name' => 'Registrado']);
        $this->provider->addServiceType($p, ['name' => 'MEXPost']);

        $p = $this->provider->create(['name' => 'UPS']);
        $this->provider->addServiceType($p, ['name' => 'Híbrido']);
    }
}
