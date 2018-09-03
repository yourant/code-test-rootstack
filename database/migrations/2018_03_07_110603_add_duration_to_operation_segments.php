<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDurationToOperationSegments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operation_segments', function (Blueprint $table) {
            $table->decimal('duration', 3, 1)->nullable()->after('name');
        });


        $segmentRepository = app()->make(\App\Repositories\Operation\SegmentRepository::class);
        foreach ($segmentRepository->all() as $segment) {
            $duration = $segment->milestones->sum('days');

            $segmentRepository->update($segment, compact('duration'));
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operation_segments', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }
}
