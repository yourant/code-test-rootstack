<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubaccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->integer('managed_by')->unsigned()->nullable()->after('timezone_id');

            $table->foreign('managed_by')->references('id')->on('clients')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('marketplaces', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('client_marketplace', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->unsigned();
            $table->integer('marketplace_id')->unsigned();

            $table->foreign('client_id')->references('id')->on('clients')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('marketplace_id')->references('id')->on('marketplaces')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('trackers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('access_token');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('client_tracker', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->unsigned();
            $table->integer('tracker_id')->unsigned();

            $table->foreign('client_id')->references('id')->on('clients')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('tracker_id')->references('id')->on('trackers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('client_tracker');
        Schema::drop('trackers');
        Schema::drop('client_marketplace');
        Schema::drop('marketplaces');

        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign('clients_managed_by_foreign');

            $table->dropColumn('managed_by');
        });
    }
}
