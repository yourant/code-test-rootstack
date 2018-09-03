<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLegsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->integer('first_checkpoint_code_id')->unsigned()->nullable()->after('provider_id');
            $table->integer('last_checkpoint_code_id')->unsigned()->nullable()->after('first_checkpoint_code_id');
            $table->integer('transit_days')->unsigned()->default(1)->after('name');
            $table->enum('type', ['last_mile', 'transit', 'other'])->nullable()->after('transit_days');

            $table->foreign('first_checkpoint_code_id')->references('id')->on('checkpoint_codes')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('last_checkpoint_code_id')->references('id')->on('checkpoint_codes')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('agreements', function (Blueprint $table) {
            $table->integer('country_id')->unsigned()->nullable()->after('client_id');
            $table->string('name')->nullable()->after('country_id');
            $table->integer('transit_days')->unsigned()->default(1)->after('name');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('legs', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('agreement_id')->unsigned();
            $table->integer('service_type_id')->unsigned();
            $table->integer('transit_days')->unsigned();
            $table->integer('sequence')->unsigned()->default(1);
            $table->boolean('controlled')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('agreement_id')->references('id')->on('agreements')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('service_type_id')->references('id')->on('service_types')->onUpdate('cascade')->onDelete('cascade');
            $table->unique(['agreement_id', 'service_type_id']);
        });

//        Schema::create('agreement_service_type', function (Blueprint $table) {
//            $table->increments('id');
//            $table->integer('agreement_id')->unsigned();
//            $table->integer('service_type_id')->unsigned();
//            $table->integer('sequence')->unsigned()->default(1);
//            $table->boolean('controlled')->default(true);
//
//            $table->foreign('agreement_id')->references('id')->on('agreements')->onUpdate('cascade')->onDelete('cascade');
//            $table->foreign('service_type_id')->references('id')->on('service_types')->onUpdate('cascade')->onDelete('cascade');
//            $table->unique(['agreement_id', 'service_type_id']);
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::drop('agreement_service_type');

        Schema::drop('legs');

        Schema::table('agreements', function (Blueprint $table) {
            $table->dropForeign('agreements_country_id_foreign');

            $table->dropColumn('country_id');
            $table->dropColumn('name');
            $table->dropColumn('transit_days');
            $table->dropSoftDeletes();
            $table->dropTimestamps();
        });

        Schema::table('service_types', function (Blueprint $table) {
            $table->dropForeign('service_types_first_checkpoint_code_id_foreign');
            $table->dropForeign('service_types_last_checkpoint_code_id_foreign');

            $table->dropColumn('first_checkpoint_code_id');
            $table->dropColumn('last_checkpoint_code_id');
            $table->dropColumn('transit_days');
            $table->dropColumn('type');
        });
    }
}
