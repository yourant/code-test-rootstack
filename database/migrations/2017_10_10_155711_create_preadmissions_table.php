<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreadmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preadmissions', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('dispatch_id')->unsigned();
            $table->string('reference');
            $table->timestamps();

            $table->foreign('dispatch_id')->references('id')->on('dispatches')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('preadmissions');
    }
}
