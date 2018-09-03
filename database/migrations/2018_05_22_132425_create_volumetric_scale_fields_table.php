<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVolumetricScaleFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('volumetric_scale_measurements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('volumetric_scale_id')->unsigned();
            $table->integer('package_id')->unsigned();
            $table->decimal('weight', 8, 3)->nullable();
            $table->decimal('width', 8, 3)->nullable();
            $table->decimal('height', 8, 3)->nullable();
            $table->decimal('length', 8, 3)->nullable();
            $table->decimal('vol_weight', 8, 3)->nullable();
            $table->text('image_url')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('volumetric_scale_id')->references('id')->on('volumetric_scales')->onUpdate('cascade')->onDelete('cascade');
            $table->index('volumetric_scale_id', 'volumetric_scale_measurements_volumetric_scale_id_foreign');

            $table->foreign('package_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('cascade');
            $table->index('package_id', 'volumetric_scale_measurements_package_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('volumetric_scale_measurements');
    }
}
