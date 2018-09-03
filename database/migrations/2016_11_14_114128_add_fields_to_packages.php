<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToPackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function(Blueprint $table) {
            $table->string('district')->nullable()->after('address3');
            $table->string('state')->nullable()->after('city');
            $table->string('shipper')->nullable()->after('email');
            $table->string('shipper_address1')->nullable()->after('shipper');
            $table->string('shipper_address2')->nullable()->after('shipper_address1');
            $table->string('shipper_city')->nullable()->after('shipper_address2');
            $table->string('shipper_state')->nullable()->after('shipper_city');
            $table->string('shipper_zip')->nullable()->after('shipper_state');
            $table->string('shipper_country')->nullable()->after('shipper_zip');
            $table->boolean('returns_allowed')->default(false)->after('service_type');
            $table->string('job_order')->nullable()->after('returns_allowed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function(Blueprint $table) {
            $table->dropColumn('district');
            $table->dropColumn('shipper');
            $table->dropColumn('shipper_address1');
            $table->dropColumn('shipper_address2');
            $table->dropColumn('shipper_city');
            $table->dropColumn('shipper_state');
            $table->dropColumn('shipper_zip');
            $table->dropColumn('shipper_country');
            $table->dropColumn('returns_allowed');
            $table->dropColumn('job_order');
        });
    }
}
