<?php

use App\Repositories\CheckpointCodeRepository;
use App\Repositories\ClassificationRepository;
use App\Repositories\CountryRepository;
use App\Repositories\ProviderRepository;
use App\Repositories\ProviderServiceTypeRepository;
use App\Repositories\ServiceTypeRepository;
use App\Repositories\TimezoneRepository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBuyerIdAndFreightToPackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var ProviderRepository $providerRepository */
        $providerRepository = app(ProviderRepository::class);

        /** @var CountryRepository $countryRepository */
        $countryRepository = app(CountryRepository::class);

        /** @var TimezoneRepository $timezoneRepository */
        $timezoneRepository = app(TimezoneRepository::class);

        /** @var ServiceTypeRepository $serviceTypeRepository */
        $serviceTypeRepository = app(ServiceTypeRepository::class);

        /** @var CheckpointCodeRepository $checkpointCodeRepository */
        $checkpointCodeRepository = app(CheckpointCodeRepository::class);

        /** @var ClassificationRepository $classificationRepository */
        $classificationRepository = app(ClassificationRepository::class);

        $brazil = $countryRepository->getByCode('BR');
        $brazil_timezone = $timezoneRepository->getByNameAndDescription('America/Sao_Paulo', '(UTC-03:00) Brasilia');

        $provider = $providerRepository->create([
            'country_id' => $brazil->id,
            'name' => 'PHX Cargo',
            'code' => 'PR8432',
            'timezone_id' => $brazil_timezone->id
        ]);

        $classification = $classificationRepository->search(['key' => 'on_distribution_to_delivery_center'])->first();

        $first_checkpoint_code = $checkpointCodeRepository->create([
            'provider_id' => $provider->id,
            'classification_id' => $classification->id,
            'key' => 'PHX-01',
            'type' => 'PHX',
            'code' => '01',
            'description' => 'Admitido',
            'category' => 'On distribution'
        ]);

        /** @var ProviderServiceTypeRepository $providerServiceTypeRepository */
        $providerServiceTypeRepository = app(ProviderServiceTypeRepository::class);
        $providerServiceType = $providerServiceTypeRepository->getByKey('distribution');

        $serviceTypeRepository->create([
            'provider_id' => $provider->id,
            'provider_service_type_id' => $providerServiceType->id,
            'first_checkpoint_code_id' => $first_checkpoint_code->id,
            'name' => 'Priority',
            'transit_days' => 10,
            'type' => 'last_mile',
            'service' => 'priority'
        ]);

        Schema::table('packages', function(Blueprint $table) {
            $table->string('buyer_id')->nullable()->after('buyer');
            $table->decimal('freight')->default(0)->after('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function(Blueprint $table) {
            $table->dropColumn('buyer_id');
            $table->dropColumn('freight');
        });
    }
}
