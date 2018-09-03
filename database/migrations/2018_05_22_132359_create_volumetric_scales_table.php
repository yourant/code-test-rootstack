<?php

use App\Repositories\VolumetricScaleRepository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVolumetricScalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('volumetric_scales', function(Blueprint $table) {
            $table->increments('id');
            $table->string('code', 10);
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        /** @var VolumetricScaleRepository $volumetricScaleRepository */
        $volumetricScaleRepository = app(VolumetricScaleRepository::class);

        $volumetricScaleRepository->create([
            'code' => 'EC001',
            'name' => 'E-Collection Volumetric Scale (1)'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('volumetric_scales');
    }
}
