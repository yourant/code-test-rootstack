<?php

use Illuminate\Database\Seeder;
use App\Repositories\AirlineRepository;
use App\Repositories\AirWaybillRepository;

class AirlinesSeeder extends Seeder
{
    protected $airlineRepository;
    protected $airWaybillRepository;

    public function __construct(AirlineRepository $airlineRepository, AirWaybillRepository $airWaybillRepository)
    {
        $this->airlineRepository = $airlineRepository;
        $this->airWaybillRepository = $airWaybillRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name' => 'EvaAir','prefix' => '695'],
            ['name' => 'EmiratesSkyCargo','prefix' => '176'],
            ['name' => 'ThaiCargo','prefix' => '217'],
            ['name' => 'CathayPacificCargo','prefix' => '160'],
            ['name' => 'SriLankanCargo','prefix' => '603'],
            ['name' => 'IagCargo','prefix' => '125'],
            ['name' => 'TurkishCargo','prefix' => '235'],
            ['name' => 'OmanAirCargo','prefix' => '910'],
            ['name' => 'MasKargo','prefix' => '232'],
            ['name' => 'QatarAirwaysCargo','prefix' => '157'],
            ['name' => 'SingaporeAirlines','prefix' => '618'],
            ['name' => 'AeromexicoCargo','prefix' => '139'],
            ['name' => 'QantasFreight','prefix' => '081'],
            ['name' => 'JetAirways','prefix' => '589'],
            ['name' => 'AsianaCargo','prefix' => '988'],
            ['name' => 'KlmCargo','prefix' => '074'],
            ['name' => 'LufthansaCargo','prefix' => '020'],
            ['name' => 'SaudiAirCargo','prefix' => '065'],
            ['name' => 'LatamCargo','prefix' => '045'],
        ];

        foreach ($data as $d) {
            $airline = $this->airlineRepository->firstOrCreate([
                'name' => $d['name'],
                'prefix' => $d['prefix']
            ]);


            $this->airWaybillRepository->search(['prefix' => $d['prefix']], false)
            ->chunk(300,function($airWaybills) use ($airline){
                foreach ($airWaybills as $airWaybill) {
                    $this->airWaybillRepository->update($airWaybill, ['airline_id' => $airline->id]);
                }
            });

        }

    }
}
