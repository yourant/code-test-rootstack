<?php

use App\Repositories\CheckpointCodeRepository;
use App\Repositories\ProviderRepository;
use Illuminate\Database\Seeder;

class AddCheckpointCodeTableSeeder extends Seeder
{

	/** @var \App\Repositories\CheckpointCodeRepository */
     protected $checkpointCodeRepository;

     /** @var \App\Repositories\ProviderRepository */
     protected $providerRepository;

      public function __construct(CheckpointCodeRepository $checkpointCodeRepository, 
        ProviderRepository $providerRepository)
    {
        $this->checkpointCodeRepository = $checkpointCodeRepository;
        $this->providerRepository = $providerRepository;
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $urbano = $this->providerRepository->getByName('Urbano');

        $this->checkpointCodeRepository->create([
          'provider_id' => $urbano->id,
          'key' => 'ALA-19',
          'description' => 'Robado o extraviado',
          'clockstop' => 1,
          'canceled' => 1
        ]);
    }
}
