<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPackageCountToBatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operation_batches', function(Blueprint $table) {
            $table->integer('processed')->unsigned()->default(0)->after('value');
            $table->integer('total')->unsigned()->default(0)->after('processed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operation_batches', function(Blueprint $table) {
            $table->dropColumn('processed');
            $table->dropColumn('total');
        });
    }
}
