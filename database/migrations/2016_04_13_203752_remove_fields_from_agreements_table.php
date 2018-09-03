<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveFieldsFromAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agreements', function(Blueprint $table) {
            $table->dropForeign('agreements_service_type_id_foreign');
            $table->dropForeign('agreements_initial_checkpoint_code_id_foreign');
            $table->dropForeign('agreements_client_id_foreign');
            $table->dropUnique('agreements_client_id_service_type_id_unique');
            $table->dropColumn(['service_type_id', 'initial_checkpoint_code_id']);

            $table->foreign('client_id')->references('id')->on('clients')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agreements', function(Blueprint $table) {
            $table->integer('service_type_id')->unsigned()->nullable()->after('client_id');
            $table->integer('initial_checkpoint_code_id')->unsigned()->nullable()->after('service_type_id');

            $table->foreign('service_type_id')->references('id')->on('service_types')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('initial_checkpoint_code_id')->references('id')->on('checkpoint_codes')->onUpdate('cascade')->onDelete('cascade');

            $table->unique(['client_id', 'service_type_id']);
        });
    }
}