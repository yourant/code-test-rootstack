<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperationPanelProviderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operation_panel_provider', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('panel_id')->unsigned();
            $table->integer('provider_id')->unsigned();
            $table->timestamps();

            $table->foreign('panel_id')->references('id')->on('operation_panels')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('provider_id')->references('id')->on('providers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('operation_panel_provider');
    }
}
