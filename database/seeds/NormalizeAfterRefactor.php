<?php

use App\Repositories\AgreementRepository;
use App\Repositories\CheckpointCodeRepository;
use App\Repositories\ClassificationRepository;
use App\Repositories\ClientRepository;
use App\Repositories\CountryRepository;
use App\Repositories\PackageRepository;
use App\Repositories\ProviderRepository;
use App\Repositories\ServiceTypeRepository;
use Illuminate\Database\Seeder;

class NormalizeAfterRefactor extends Seeder
{
    /**
     * @var CountryRepository
     */
    protected $country;

    /**
     * @var ProviderRepository
     */
    protected $provider;

    /**
     * @var CheckpointCodeRepository
     */
    protected $checkpoint_code;

    /**
     * @var ClassificationRepository
     */
    protected $classification;

    /**
     * @var ServiceTypeRepository
     */
    protected $service_type;

    /**
     * @var ClientRepository
     */
    protected $client;

    /**
     * @var AgreementRepository
     */
    protected $agreement;

    /**
     * @var PackageRepository
     */
    protected $package;

    public function __construct(CountryRepository $country, ProviderRepository $provider, CheckpointCodeRepository $checkpoint_code, ClassificationRepository $classification, ServiceTypeRepository $service_type, ClientRepository $client, AgreementRepository $agreement, PackageRepository $package)
    {
        $this->country = $country;
        $this->provider = $provider;
        $this->checkpoint_code = $checkpoint_code;
        $this->classification = $classification;
        $this->service_type = $service_type;
        $this->client = $client;
        $this->agreement = $agreement;
        $this->package = $package;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
//            DB::beginTransaction();

            // Basic data
            $client = $this->client->getByName('Sinoair');
            $country = $this->country->search(['name' => 'China'])->first();
            $mx_country = $this->country->search(['code' => 'MX'])->first();
            $co_country = $this->country->search(['code' => 'CO'])->first();
            $br_country = $this->country->search(['code' => 'BR'])->first();
            $this->client->update($client, ['name' => 'Aliexpress', 'country_id' => $country->id]);
            $mx_agreement = $this->agreement->getById(7);
            $this->agreement->update($mx_agreement, ['country_id' => $mx_country->id, 'name' => 'Mexico Standard', 'transit_days' => 17]);
            $co_agreement = $this->agreement->getById(8);
            $this->agreement->update($co_agreement, ['country_id' => $co_country->id, 'name' => 'Colombia Standard', 'transit_days' => 15]);

            $test_mx_agreement = $this->agreement->getById(13);
            $this->agreement->update($test_mx_agreement, ['country_id' => $mx_country->id, 'name' => 'Mexico Standard', 'transit_days' => 12]);
            $test_br_agreement = $this->agreement->getById(16);
            $this->agreement->update($test_br_agreement, ['country_id' => $br_country->id, 'name' => 'Brasil Standard', 'transit_days' => 12]);


            $cl = $this->classification->create(['key' => 'received_at_office_of_exchange', 'name' => 'Received at office of exchange', 'type' => 'Customs', 'order' => 8]);

            // Global Match
            $p = $this->provider->create(['name' => 'Global Match', 'country_id' => 229, 'timezone_id' => 39]);
            // -- Checkpoints codes
            $g1 = $this->checkpoint_code->create(['provider_id' => $p->id, 'description' => 'Picked up at airport', 'classification_id' => 3, 'key' => 'GM-1']);
            $g2 = $this->checkpoint_code->create(['provider_id' => $p->id, 'description' => 'Received at the warehouse', 'classification_id' => 3, 'key' => 'GM-2']);
            $g3 = $this->checkpoint_code->create(['provider_id' => $p->id, 'description' => 'Departed to destination country (MEX)', 'classification_id' => 4, 'key' => 'GM-3']);
            $g4 = $this->checkpoint_code->create(['provider_id' => $p->id, 'description' => 'Arrived at destination country (MEX)', 'classification_id' => 6, 'key' => 'GM-4']);
            $g5 = $this->checkpoint_code->create(['provider_id' => $p->id, 'description' => 'Departed to destination country (BOG)', 'classification_id' => 4, 'key' => 'GM-5']);
            $g6 = $this->checkpoint_code->create(['provider_id' => $p->id, 'description' => 'Arrived at destination country (BOG)', 'classification_id' => 6, 'key' => 'GM-6']);
            // -- Service Types
            $st_gm1 = $this->provider->addServiceType($p, ['first_checkpoint_code_id' => $g1->id, 'last_checkpoint_code_id' => $g4->id, 'name' => 'LHR to MEX via Aeroméxico', 'transit_days' => 3, 'type' => 'transit']);
            $st_gm2 = $this->provider->addServiceType($p, ['first_checkpoint_code_id' => $g1->id, 'last_checkpoint_code_id' => $g6->id, 'name' => 'LHR to BOG via Avianca', 'transit_days' => 3, 'type' => 'transit']);

            // Sinotrans
            $p = $this->provider->create(['name' => 'Sinotrans', 'country_id' => 45, 'timezone_id' => 108]);
            // -- Checkpoints codes
            $s1 = $this->checkpoint_code->create(['provider_id' => $p->id, 'description' => 'Posted at origin', 'classification_id' => 1, 'key' => 'SIN-1']);
            $this->checkpoint_code->create(['provider_id' => $p->id, 'description' => 'Freight checked in at departure airline (HGH)', 'classification_id' => 2, 'key' => 'SIN-2']);
            $this->checkpoint_code->create(['provider_id' => $p->id, 'description' => 'Freight departed to destination (TPE)', 'classification_id' => 2, 'key' => 'SIN-3']);
            $this->checkpoint_code->create(['provider_id' => $p->id, 'description' => 'Freight departed to destination (LHR)', 'classification_id' => 2, 'key' => 'SIN-4']);
            $s2 = $this->checkpoint_code->create(['provider_id' => $p->id, 'description' => 'Freight arrived at destination (TPE)', 'classification_id' => 2, 'key' => 'SIN-5']);
            $this->checkpoint_code->create(['provider_id' => $p->id, 'description' => 'Freight arrived at destination (LHR)', 'classification_id' => 2, 'key' => 'SIN-6']);
            $s3 = $this->checkpoint_code->create(['provider_id' => $p->id, 'description' => 'Consignment actual received after break down', 'classification_id' => 2, 'key' => 'SIN-7']);
            $s4 = $this->checkpoint_code->create(['provider_id' => $p->id, 'description' => 'Freight delivered to forwarder', 'classification_id' => 2, 'key' => 'SIN-8']);
            // -- Service Types
            $st_sin1 = $this->provider->addServiceType($p, ['first_checkpoint_code_id' => $s1->id, 'last_checkpoint_code_id' => $s4->id, 'name' => 'China to LHR via EVA Airways', 'transit_days' => 4, 'type' => 'transit']);

            // Serpost
            $p = $this->provider->create(['name' => 'Serpost', 'country_id' => 172, 'timezone_id' => 21]);
            // -- Checkpoints codes
            $cc1 = $this->checkpoint_code->create(['provider_id' => $p->id, 'classification_id' => $cl->id, 'description' => 'Admitido', 'key' => 'MLA-20', 'type' => 'MLA', 'code' => 20]);
            // -- Service Types
            $st_ser1 = $this->provider->addServiceType($p, ['first_checkpoint_code_id' => $cc1->id, 'name' => 'Registered', 'transit_days' => 7, 'type' => 'last_mile']);

            // Correos Chile
            $p = $this->provider->create(['name' => 'Correos de Chile', 'country_id' => 44, 'timezone_id' => 29]);
            // -- Checkpoints codes
            $cc1 = $this->checkpoint_code->create(['provider_id' => $p->id, 'classification_id' => $cl->id, 'description' => 'Admitido', 'key' => 'MLA-20', 'type' => 'MLA', 'code' => 20]);
            // -- Service Types
            $st_ch1 = $this->provider->addServiceType($p, ['first_checkpoint_code_id' => $cc1->id, 'name' => 'Registered', 'transit_days' => 7, 'type' => 'last_mile']);

            // Sepomex
            $p = $this->provider->getByName('Correos de México');
            $cm1 = $this->checkpoint_code->create(['provider_id' => $p->id, 'classification_id' => $cl->id, 'description' => 'Admitido', 'key' => 'MLA-20', 'type' => 'MLA', 'code' => 20]);

            // Correios de Brasil
            $p = $this->provider->getByName('Correios de Brasil');
            $cb1 = $this->checkpoint_code->create(['provider_id' => $p->id, 'classification_id' => $cl->id, 'description' => 'Admitido', 'key' => 'MLA-20', 'type' => 'MLA', 'code' => 20]);

            // Normalize Service types

            // ** Sepomex Registrado
            $mx_st_registrado = $this->service_type->getById(1);
            $this->service_type->update($mx_st_registrado, ['transit_days' => 10, 'type' => 'last_mile', 'first_checkpoint_code_id' => $cm1->id]);

            // ** Sepomex MEXPost
            $mx_st_mexpost = $this->service_type->getById(2);
            $this->service_type->update($mx_st_mexpost, ['transit_days' => 6, 'type' => 'last_mile', 'first_checkpoint_code_id' => $cm1->id]);

            // ** Sepomex Hibrido
            $mx_st_hibrido = $this->service_type->getById(3);
            $this->service_type->forceDelete($mx_st_hibrido);

            // ** 4-72 Registrado
            $co_st_registrado = $this->service_type->getById(4);
            $this->service_type->update($co_st_registrado, ['transit_days' => 7, 'type' => 'last_mile', 'first_checkpoint_code_id' => 55]);

            // ** Correios Registrado
            $br_st_registrado = $this->service_type->getById(6);
            $this->service_type->update($br_st_registrado, ['transit_days' => 10, 'type' => 'last_mile', 'first_checkpoint_code_id' => $cb1->id]);

            // LEGS
            // ** Mexico Standard (Aliexpress)
            $this->agreement->addLeg($mx_agreement, ['service_type_id' => $st_sin1->id, 'transit_days' => 4, 'sequence' => 1, 'controlled' => false]);
            $this->agreement->addLeg($mx_agreement, ['service_type_id' => $st_gm1->id, 'transit_days' => 3, 'sequence' => 2, 'controlled' => true]);
            $this->agreement->addLeg($mx_agreement, ['service_type_id' => $mx_st_registrado->id, 'transit_days' => 10, 'sequence' => 3, 'controlled' => true]);

            // ** Colombia Standard (Aliexpress)
            $this->agreement->addLeg($co_agreement, ['service_type_id' => $st_sin1->id, 'transit_days' => 4, 'sequence' => 1, 'controlled' => false]);
            $this->agreement->addLeg($co_agreement, ['service_type_id' => $st_gm2->id, 'transit_days' => 3, 'sequence' => 2, 'controlled' => true]);
            $this->agreement->addLeg($co_agreement, ['service_type_id' => $co_st_registrado->id, 'transit_days' => 8, 'sequence' => 3, 'controlled' => true]);

            // ** Mexico Standard (Test)
            $this->agreement->addLeg($test_mx_agreement, ['service_type_id' => $mx_st_registrado->id, 'transit_days' => 10, 'sequence' => 1, 'controlled' => true]);

            // ** Brasil Standard (Test)
            $this->agreement->addLeg($test_br_agreement, ['service_type_id' => $br_st_registrado->id, 'transit_days' => 10, 'sequence' => 1, 'controlled' => true]);

            // Delete Received at the warehouse
            DB::delete('delete c from checkpoints as c inner join packages as p on c.package_id = p.id inner join bags as b on p.bag_id = b.id inner join dispatches as d on b.dispatch_id = d.id where d.agreement_id = ? and c.checkpoint_code_id = ?', [$test_mx_agreement->id, 43]);
            DB::delete('delete c from checkpoints as c inner join packages as p on c.package_id = p.id inner join bags as b on p.bag_id = b.id inner join dispatches as d on b.dispatch_id = d.id where d.agreement_id = ? and c.checkpoint_code_id = ?', [$test_br_agreement->id, 68]);


            // ALIEXPRESS
            // -- Update checkpoints (MEXICO)
            //    ** Posted at origin 52
            DB::update('update checkpoints as c inner join packages as p on c.package_id = p.id inner join bags as b on p.bag_id = b.id inner join dispatches as d on b.dispatch_id = d.id inner join agreements as a on d.agreement_id = a.id set	c.checkpoint_code_id = ? where a.client_id = ? and a.country_id = ? and c.checkpoint_code_id = ?', [$s1->id, $mx_agreement->client_id, $mx_agreement->country_id, 52]);
            //    ** On transit between airports 50
//            DB::update('update checkpoints as c inner join packages as p on c.package_id = p.id inner join bags as b on p.bag_id = b.id inner join dispatches as d on b.dispatch_id = d.id inner join agreements as a on d.agreement_id = a.id set	c.checkpoint_code_id = ? where a.client_id = ? and a.country_id = ? and c.checkpoint_code_id = ?', [$s2->id, $mx_agreement->client_id, $mx_agreement->country_id, 50]);
            //    ** Received at the warehouse 43
            DB::update('update checkpoints as c inner join packages as p on c.package_id = p.id inner join bags as b on p.bag_id = b.id inner join dispatches as d on b.dispatch_id = d.id inner join agreements as a on d.agreement_id = a.id set	c.checkpoint_code_id = ? where a.client_id = ? and a.country_id = ? and c.checkpoint_code_id = ?', [$g2->id, $mx_agreement->client_id, $mx_agreement->country_id, 43]);
            //    ** Departed to destination country 44
            DB::update('update checkpoints as c inner join packages as p on c.package_id = p.id inner join bags as b on p.bag_id = b.id inner join dispatches as d on b.dispatch_id = d.id inner join agreements as a on d.agreement_id = a.id set	c.checkpoint_code_id = ? where a.client_id = ? and a.country_id = ? and c.checkpoint_code_id = ?', [$g3->id, $mx_agreement->client_id, $mx_agreement->country_id, 44]);
            //    ** Arrived at destination country 17
            DB::update('update checkpoints as c inner join packages as p on c.package_id = p.id inner join bags as b on p.bag_id = b.id inner join dispatches as d on b.dispatch_id = d.id inner join agreements as a on d.agreement_id = a.id set	c.checkpoint_code_id = ? where a.client_id = ? and a.country_id = ? and c.checkpoint_code_id = ?', [$g4->id, $mx_agreement->client_id, $mx_agreement->country_id, 17]);

            // Air Waybills
            DB::update('update air_waybills as a set a.departed_at = null, a.arrived_at = null, a.delivered_at = null');

            // Add missing AWBs and reset tracking status
            foreach (['695-38460494' => 4041,'695-38460483' => 4040,'695-38460472' => 4039,'695-38460461' => 4038,'695-38460450' => 4037,'695-38460446' => 4036,'695-38460391' => 4035,'695-38460380' => 4034,'695-38460376' => 4033,'695-38460365' => 4032,'695-38460354' => 4031,'695-38460343' => 4030,'695-38191090' => 4029,'695-38191086' => 4028,'695-38191075' => 4027] as $code => $dispatch_number) {
                DB::insert('insert into air_waybills (code) values (?)', [$code]);
                $id = DB::getPdo()->lastInsertId();
                DB::update('update dispatches as d set d.air_waybill_id = ? where d.agreement_id = ? and d.number = ?', [$id, $mx_agreement->id, $dispatch_number]);
                DB::update('update dispatches as d set d.air_waybill_id = ? where d.agreement_id = ? and d.number = ?', [$id, $co_agreement->id, $dispatch_number]);
            }

            // Delete legacy Packages
            DB::delete('delete p, b, d, a from packages as p inner join bags as b on p.bag_id = b.id inner join dispatches as d on b.dispatch_id = d.id inner join agreements as a on d.agreement_id = a.id inner join clients as c on a.client_id = c.id where c.name = ? or c.name = ? or c.name = ?', ['I-Parcel', 'ABOL', 'UKP Worldwide']);


            // Set default legs
            $count = 0;
            $this->package->search()->chunk(500, function ($packages) use (&$count) {
                /** @var Package $package */
                foreach ($packages as $package) {
                    $res = DB::select("select substring_index(group_concat(l.id order by l.sequence asc), ',', 1) as first_leg_id from packages as p inner join bags as b on p.bag_id = b.id inner join dispatches as d on b.dispatch_id = d.id inner join legs as l on l.agreement_id = d.agreement_id where p.id = ?", [$package->id]);
                    if ($leg_id = $res[0]->first_leg_id) {
                        DB::update('update packages set leg_id = ? where id = ?', [$leg_id, $package->id]);
                        ++$count;
                    }
                }

                echo "Processed {$count} packages" . PHP_EOL;
            });


//            DB::commit();
        } catch (Exception $e) {
//            DB::rollBack();
            throw $e;
        }
    }
}