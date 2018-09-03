<?php

use App\Repositories\ProviderRepository;
use App\Repositories\ProviderServiceTypeRepository;
use App\Repositories\ServiceTypeRepository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProviderServiceTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_service_types', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('key');
            $table->string('name');
        });

        Schema::table('service_types', function (Blueprint $table) {
            $table->integer('provider_service_type_id')->unsigned()->nullable()->after('provider_id');
            $table->string('code', 6)->nullable()->after('last_checkpoint_code_id');
            $table->string('details')->nullable()->after('service');

            $table->foreign('provider_service_type_id')->references('id')->on('provider_service_types')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('providers', function (Blueprint $table) {
            $table->string('code', 6)->nullable()->after('name');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->string('origin_warehouse_code', 6)->nullable()->after('length');
        });

        $provider_codes = [
            ['provider_name' => 'Correos de México', 'provider_code' => 'PR4937'],
            ['provider_name' => '4-72', 'provider_code' => 'PR3548'],
            ['provider_name' => 'Correios de Brasil', 'provider_code' => 'PR3289'],
            ['provider_name' => 'UPS', 'provider_code' => 'PR8147'],
            ['provider_name' => 'Global Match', 'provider_code' => 'PR0538'],
            ['provider_name' => 'Sinotrans', 'provider_code' => 'PR5573'],
            ['provider_name' => 'Serpost', 'provider_code' => 'PR1985'],
            ['provider_name' => 'Correos de Chile', 'provider_code' => 'PR2785'],
            ['provider_name' => 'CH Robinson', 'provider_code' => 'PR0385'],
            ['provider_name' => 'Aeroméxico', 'provider_code' => 'PR7814'],
            ['provider_name' => 'APC', 'provider_code' => 'PR6986'],
            ['provider_name' => 'Globegistics', 'provider_code' => 'PR1747'],
            ['provider_name' => 'MO Delivering', 'provider_code' => 'PR3158'],
            ['provider_name' => 'Seller Drop Off', 'provider_code' => 'PR3278'],
            ['provider_name' => 'LHR Warehouse', 'provider_code' => 'PR9685'],
            ['provider_name' => 'Correos del Ecuador', 'provider_code' => 'PR7946'],
            ['provider_name' => 'Blue Express', 'provider_code' => 'PR6548'],
            ['provider_name' => 'Urbano', 'provider_code' => 'PR6749']
        ];

        $provider_service_types = [
            ['key' => 'warehouse', 'name' => 'Warehouse'],
            ['key' => 'distribution', 'name' => 'Distribution'],
            ['key' => 'transit', 'name' => 'Transit'],
        ];

        /** @var ProviderServiceTypeRepository $providerServiceTypeRepository */
        $providerServiceTypeRepository = app(ProviderServiceTypeRepository::class);
        
        /** @var ServiceTypeRepository $serviceTypeRepository */
        $serviceTypeRepository = app(ServiceTypeRepository::class);

        /** @var ProviderRepository $providerRepository */
        $providerRepository = app(ProviderRepository::class);
        
        foreach ($provider_codes as $provider_code) {
            $provider = $providerRepository->getByName($provider_code['provider_name']);
            if ($provider) {
                $providerRepository->update($provider, ['code' => $provider_code['provider_code']]);
            }
        }

        Schema::table('providers', function (Blueprint $table) {
            $table->string('code', 6)->unique()->nullable()->change();
        });

        foreach ($provider_service_types as $provider_service_type) {
            $providerServiceTypeRepository->create($provider_service_type);
        }
        
        $last_miles = $serviceTypeRepository->search(['type' => 'last_mile'])->get();
        $last_mile_key = $providerServiceTypeRepository->getByKey('distribution');
        foreach ($last_miles as $last_mile) {
            $serviceTypeRepository->update($last_mile, ['provider_service_type_id' => $last_mile_key->id]);
        }

        $transits = $serviceTypeRepository->search(['type' => 'transit'])->get();
        $transit_key = $providerServiceTypeRepository->getByKey('transit');
        foreach ($transits as $transit) {
            $serviceTypeRepository->update($transit, ['provider_service_type_id' => $transit_key->id]);
        }

        $sinotrans = $providerRepository->getByName('Sinotrans');
        $sinotrans_service_types = $serviceTypeRepository->search(['provider_id' => $sinotrans->id])->get();
        $warehouse_key = $providerServiceTypeRepository->getByKey('warehouse');
        foreach ($sinotrans_service_types as $sinotrans_service_type) {
            $serviceTypeRepository->update($sinotrans_service_type, ['provider_service_type_id' => $warehouse_key->id]);
        }

        // Add new Sinotrans Warehouses and codes
        $sinotrans_service_type = $serviceTypeRepository->search(['provider_id' => $sinotrans->id])->first();
        $serviceTypeRepository->update($sinotrans_service_type, ['code' => 'WH5841', 'name' => 'Shenzhen Warehouse']);

        $new_warehouse_1 = $sinotrans_service_type->replicate();
        $new_warehouse_2 = $sinotrans_service_type->replicate();

        $new_warehouse_1->name = 'Hong Kong Warehouse';
        $new_warehouse_1->code = 'WH5842';

        $new_warehouse_2->name = 'Hangzhou Warehouse';
        $new_warehouse_2->code = 'WH5843';

        $new_warehouse_1->save();
        $new_warehouse_2->save();


        // Add E-Collection as Provider and add service types

        $ecollection_provider = $providerRepository->create([
            'name' => 'E-Collection',
            'code' => 'PR6719',
            'country_id' => $sinotrans->country_id,
            'timezone_id' => $sinotrans->timezone_id
        ]);

        $ecollection_warehouse = $sinotrans_service_type->replicate();
        $ecollection_warehouse->code = 'WH3154';
        $ecollection_warehouse->name = 'E-Collection Warehouse';
        $ecollection_warehouse->provider_id = $ecollection_provider->id;
        $ecollection_warehouse->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
