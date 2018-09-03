<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBillableFieldsToPackagesTable extends Migration
{
//    use DispatchesJobs;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function(Blueprint $table) {
            $table->decimal('billable_weight', 8, 3)->nullable()->after('calculated_vol_weight');
            $table->string('billable_method', 12)->nullable()->after('billable_weight');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('billable_weight');
            $table->dropColumn('billable_method');
        });
    }
}