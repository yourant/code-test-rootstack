<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDispatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_service_type', function(Blueprint $table) {
            $table->dropForeign('client_service_type_service_type_id_foreign');
            $table->dropForeign('client_service_type_client_id_foreign');
            $table->dropUnique('client_service_type_client_id_service_type_id_unique');
        });

        Schema::rename('client_service_type', 'agreements');

        Schema::table('agreements', function(Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('service_type_id')->references('id')->on('service_types')->onUpdate('cascade')->onDelete('cascade');

            $table->unique(['client_id', 'service_type_id']);
        });

        Schema::create('dispatches', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('agreement_id')->unsigned()->nullable();
            $table->integer('number');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['number', 'agreement_id']);

            $table->foreign('agreement_id')->references('id')->on('agreements')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('bags', function(Blueprint $table) {
            $table->integer('dispatch_id')->unsigned()->nullable()->after('id');

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
        Schema::table('bags', function(Blueprint $table){
            $table->dropForeign('bags_dispatch_id_foreign');
            $table->dropColumn('dispatch_id');
        });

        Schema::drop('dispatches');
    }
}
