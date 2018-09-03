<?php

use App\Dispatch;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddYearToDispatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->integer('year')->unsigned()->nullable()->after('number');

            $table->dropUnique('dispatches_number_agreement_id_unique');
        });

        foreach (Dispatch::all() as $d) {
            $year = $d->created_at->year;
            $d->update(compact('year'));
        }

        Schema::table('dispatches', function (Blueprint $table) {
            $table->unique(['agreement_id', 'number', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->dropUnique('dispatches_agreement_id_number_year_unique');
            $table->dropColumn('year');
            $table->unique(['number', 'agreement_id']);
        });
    }
}
