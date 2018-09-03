<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReestructureAlertsAndClassifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->integer('provider_id')->unsigned()->nullable()->after('id');

            $table->foreign('provider_id')->references('id')->on('providers')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('checkpoint_codes', function (Blueprint $table) {
            $table->integer('classification_id')->unsigned()->nullable()->after('provider_id');
            $table->string('key')->nullable()->after('classification_id');

            $table->foreign('classification_id')->references('id')->on('classifications')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('classifications', function (Blueprint $table) {
            $table->string('type')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('classifications', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('checkpoint_codes', function (Blueprint $table) {
            $table->dropForeign('checkpoint_codes_classification_id_foreign');
            $table->dropColumn('key');
            $table->dropColumn('classification_id');
        });

        Schema::table('alerts', function (Blueprint $table) {
            $table->dropForeign('alerts_provider_id_foreign');
            $table->dropColumn('provider_id');
        });
    }
}
