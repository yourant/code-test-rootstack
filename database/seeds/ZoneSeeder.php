<?php

use App\Repositories\ZoneRepository;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder {

    public function __construct(ZoneRepository $zone)
    {
        $this->zone = $zone;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->zone->create(["name" => "Rural"]);
        $this->zone->create(["name" => "Semiurbano"]);
        $this->zone->create(["name" => "Urbano"]);
    }
}