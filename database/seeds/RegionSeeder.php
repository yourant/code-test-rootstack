<?php

use App\Models\Country;
use App\Repositories\CountryRepository;
use App\Repositories\RegionRepository;
use App\Repositories\AdminLevel1Repository as StateRepository;
use App\Models\State;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{

    /**
     * @var RegionRepository
     */
    protected $regionRepository;

    /**
     * @var CountryRepository
     */
    protected $countryRepository;

    /**
     * @var StateRepository
     */
    protected $stateRepository;

    public function __construct(RegionRepository $regionRepository, CountryRepository $countryRepository, StateRepository $stateRepository)
    {
        $this->regionRepository = $regionRepository;
        $this->countryRepository = $countryRepository;
        $this->stateRepository = $stateRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var Country $country */
        foreach ($this->countryRepository->all() as $country) {
            /** @var State $state */
            foreach ($country->states as $state) {

                if ($state->region) {
                    $region = $this->regionRepository->firstOrCreate(['name' => $state->region, 'country_id' => $state->country_id]);
                    $this->stateRepository->update($state, ['region_id' => $region->id]);
                }

            }
        }
    }
}
