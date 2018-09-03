<?php

use App\Repositories\ServiceTypeRepository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProviderCodeToServiceTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_types', function(Blueprint $table) {
            $table->string('provider_code')->nullable()->after('name');
        });

        /** @var ServiceTypeRepository $serviceTypeRepository */
        $serviceTypeRepository = app(ServiceTypeRepository::class);

        $ecollection_shenzhen = $serviceTypeRepository->search(['code' => 'WH3154'])->first();

        $serviceTypeRepository->update($ecollection_shenzhen, [
            'provider_code' => 'SZX',
            'name' => 'E-Collection Shenzhen Warehouse'
        ]);

        $serviceTypeRepository->create([
            'provider_code' => 'HKG',
            'name' => 'E-Collection Hong Kong Warehouse',
            'provider_id' => $ecollection_shenzhen->provider_id,
            'provider_service_type_id' => $ecollection_shenzhen->provider_service_type_id,
            'first_checkpoint_code_id' => $ecollection_shenzhen->first_checkpoint_code_id,
            'last_checkpoint_code_id ' => $ecollection_shenzhen->last_checkpoint_code_id,
            'code' => 'WH3155',
            'transit_days' => $ecollection_shenzhen->transit_days,
            'type' => $ecollection_shenzhen->type,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_types', function(Blueprint $table) {
            $table->dropColumn('provider_code');
        });
    }
}
