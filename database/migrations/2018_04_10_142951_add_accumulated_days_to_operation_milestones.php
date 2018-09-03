<?php

use App\Models\Operation\Milestone;
use App\Models\Operation\Panel;
use App\Models\Operation\Segment;
use App\Models\Operation\StateMilestone;
use App\Repositories\Operation\MilestoneRepository;
use App\Repositories\Operation\PanelRepository;
use App\Repositories\Operation\SegmentRepository;
use App\Repositories\Operation\StateMilestoneRepository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccumulatedDaysToOperationMilestones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operation_milestones', function (Blueprint $table) {
            $table->decimal('accumulated_days', 3, 1)->nullable()->after('days');
        });

        Schema::table('operation_state_milestones', function (Blueprint $table) {
            $table->decimal('accumulated_days', 3, 1)->nullable()->after('days');
        });

        /** @var PanelRepository $panelRepository */
        $panelRepository = app(PanelRepository::class);

        /** @var MilestoneRepository $milestoneRepository */
        $milestoneRepository = app(MilestoneRepository::class);
        
        /** @var SegmentRepository $segmentRepository */
        $segmentRepository = app(SegmentRepository::class);

        /** @var StateMilestoneRepository $stateMilestoneRepository */
        $stateMilestoneRepository = app(StateMilestoneRepository::class);

        $panels = $panelRepository->all();

        /** @var Panel $panel */
        foreach ($panels as $panel) {

            $accumulated_days = 0;

            /** @var Segment $segment */
            foreach ($panel->segments as $segment) {

                if ($segment->isSegmentTypeOfPickAndPack()) {
                    /** @var Milestone $milestone */
                    foreach ($segment->milestones()->get() as $milestone) {
                        $milestoneRepository->update($milestone, ['accumulated_days' => 0, 'days' => 20]);
                    }
                    $segmentRepository->update($segment, ['duration' => 20]);
                    continue;
                }

                $total_segment_days = 0;

                $order_direction = 'desc';
                if ($segment->isSegmentTypeOfLastMile()) {
                    $order_direction = 'asc';
                }
                
                /** @var Milestone $milestone */
                foreach ($segment->milestones()->orderBy('position', $order_direction)->get() as $milestone) {
                    if ($milestone->hasStates()) {
                        /** @var StateMilestone $stateMilestone */
                        foreach ($milestone->stateMilestones()->get() as $stateMilestone) {
                            $stateMilestoneRepository->update($stateMilestone, ['accumulated_days' => ($accumulated_days + $milestone->days), 'days' => $milestone->days]);
                        }
                    }
                    $total_segment_days = $total_segment_days + $milestone->days;
                    $accumulated_days = $accumulated_days + $milestone->days;
                    $milestoneRepository->update($milestone, ['accumulated_days' => $accumulated_days]);
                }
                $segmentRepository->update($segment, ['duration' => $total_segment_days]);
            }
        }
        
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operation_milestones', function (Blueprint $table) {
            $table->dropColumn('accumulated_days');
        });

        Schema::table('operation_state_milestones', function (Blueprint $table) {
            $table->dropColumn('accumulated_days');
        });
    }
}
