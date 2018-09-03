<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFailedJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->dropColumn('failed_at');
        });
        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->longText('payload')->change();
            $table->longText('exception')->after('payload');
            $table->timestamp('failed_at')->useCurrent();
        });
        Schema::table('jobs', function(Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->dropColumn('reserved');

            $table->index(['queue', 'reserved_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jobs', function(Blueprint $table) {
            $table->increments('id')->change();
            $table->unsignedTinyInteger('reserved')->after('attempts');

            $table->dropIndex('jobs_queue_reserved_at_index');
        });

        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->increments('id')->change();
            $table->text('payload')->change();
            $table->dropColumn('exception');
            $table->dropColumn('failed_at');
        });
        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->timestamp('failed_at');
        });
    }
}
