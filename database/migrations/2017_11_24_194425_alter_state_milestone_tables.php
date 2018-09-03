<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStateMilestoneTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $stateMilestoneMetricRepository = app(\App\Repositories\Operation\StateMilestoneMetricRepository::class);
        $stateMilestoneMetricRepository->truncate();

        $stateMilestoneRepository = app(\App\Repositories\Operation\StateMilestoneRepository::class);
        $stateMilestoneRepository->truncate();

        Schema::table('operation_state_milestones', function (Blueprint $table) {
            $table->renameColumn('transit', 'days');
            $table->dropColumn('distribution');

            $table->dropForeign('operation_state_milestones_segment_id_foreign');

            $table->renameColumn('segment_id', 'milestone_id');
            $table->foreign('milestone_id')->references('id')->on('operation_milestones')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('operation_state_milestone_metrics', function (Blueprint $table) {
            $table->dropColumn('en_route');
            $table->dropColumn('customs_to_postal_office');
            $table->dropColumn('mailman');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
