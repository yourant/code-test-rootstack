<?php

use App\Repositories\ClientRepository;
use App\Repositories\TimezoneRepository;
use Illuminate\Database\Seeder;

class ClientTableSeeder extends Seeder {

    public function __construct(ClientRepository $client, TimezoneRepository $timezone)
    {
        $this->client = $client;
        $this->timezone = $timezone;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tz = $this->timezone->getByName('US/Eastern');
        $this->client->create(['name' => 'I-Parcel', 'country' => 'United States', 'timezone_id' => $tz->id]);
    }
}