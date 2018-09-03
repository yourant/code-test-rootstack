<?php

use App\Repositories\AirWaybillRepository;
use App\Repositories\ProviderRepository;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrefixToProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->string('prefix', 10)->nullable();
        });

        Schema::table('air_waybills', function (Blueprint $table) {
            $table->integer('provider_id')->unsigned()->nullable();

            $table->foreign('provider_id')->references('id')->on('providers')->onUpdate('cascade')->onDelete('cascade');
            $table->index('provider_id', 'air_waybills_provider_id_foreign');
        });

        $this->seed();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('air_waybills', function (Blueprint $table) {
            $table->dropIndex('air_waybills_provider_id_foreign');
            $table->dropForeign(['provider_id']);
            $table->dropColumn('provider_id');
        });

        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn('prefix');
        });
    }

    protected function seed()
    {
        /** @var ProviderRepository $providerRepository */
        $providerRepository = app(ProviderRepository::class);

        /** @var AirWaybillRepository $airWaybillRepository */
        $airWaybillRepository = app(AirWaybillRepository::class);

        $providers = [
            ['code' => 'PR0521', 'name' => 'Eva Air', 'prefix' => '695'],
            ['code' => 'PR0522', 'name' => 'Emirates SkyCargo', 'prefix' => '176'],
            ['code' => 'PR0523', 'name' => 'Thai Cargo', 'prefix' => '217'],
            ['code' => 'PR0524', 'name' => 'Cathay Pacific Cargo', 'prefix' => '160'],
            ['code' => 'PR0525', 'name' => 'Sri Lankan Cargo', 'prefix' => '603'],
            ['code' => 'PR0526', 'name' => 'Iag Cargo', 'prefix' => '125'],
            ['code' => 'PR0527', 'name' => 'Turkish Cargo', 'prefix' => '235'],
            ['code' => 'PR0528', 'name' => 'Oman Air Cargo', 'prefix' => '910'],
            ['code' => 'PR0529', 'name' => 'Mas Kargo', 'prefix' => '232'],
            ['code' => 'PR0530', 'name' => 'Qatar Airways Cargo', 'prefix' => '157'],
            ['code' => 'PR0531', 'name' => 'Singapore Airlines', 'prefix' => '618'],
            ['code' => 'PR7814', 'name' => 'Aeromexico', 'prefix' => '139'],
            ['code' => 'PR0532', 'name' => 'Qantas Freight', 'prefix' => '081'],
            ['code' => 'PR0533', 'name' => 'JetAirways', 'prefix' => '589'],
            ['code' => 'PR0534', 'name' => 'Asiana Cargo', 'prefix' => '988'],
            ['code' => 'PR0535', 'name' => 'KlmCargo', 'prefix' => '074'],
            ['code' => 'PR0536', 'name' => 'Lufthansa Cargo', 'prefix' => '020'],
            ['code' => 'PR0537', 'name' => 'Saudi Air Cargo', 'prefix' => '065'],
            ['code' => 'PR0538', 'name' => 'Latam Cargo', 'prefix' => '045'],
        ];

        foreach ($providers as $provider) {
            $p = $providerRepository->updateOrCreate(['code' => $provider['code']], $provider);

            $airWaybillRepository
                ->search(['prefix' => $p->prefix], false)
                ->chunk(100, function ($airWaybills) use ($airWaybillRepository, $p) {
                    foreach ($airWaybills as $airWaybill) {
                        $airWaybillRepository->update($airWaybill, ['provider_id' => $p->id]);
                    }
                });

        }
    }
}