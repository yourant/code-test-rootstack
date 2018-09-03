<?php

namespace App\Repositories\Operation;

use App\Repositories\AbstractRepository;
use App\Models\Operation\Panel;
use DB;

class PanelRepository extends AbstractRepository
{
    function __construct(Panel $model)
    {
        $this->model = $model;
    }

    public function search(array $filters = [])
    {
        $query = $this->model
                ->select('operation_panels.*');

        return $query;
    }

    public function getSummary()
    {
        return $this->model
            ->select('operation_panels.*')
            ->addSelect(DB::raw("count(operation_metrics.package_id) as package_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.stalled <= operation_milestones.days) and (operation_metrics.stalled <= (operation_milestones.days - operation_milestones.warning1))  then 1 else 0 end) as warning1_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.stalled <= operation_milestones.days) and (operation_metrics.stalled > (operation_milestones.days - operation_milestones.warning1))   and (operation_metrics.stalled <= (operation_milestones.days - operation_milestones.warning2)) then 1 else 0 end) as warning2_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.stalled > operation_milestones.days)  and (operation_metrics.stalled >= (operation_milestones.critical1 + operation_milestones.days)) and (operation_metrics.stalled < (operation_milestones.critical2 + operation_milestones.days)) then 1 else 0 end) as critical1_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.stalled > operation_milestones.days)  and (operation_metrics.stalled >= (operation_milestones.critical2 + operation_milestones.days)) and (operation_metrics.stalled < (operation_milestones.critical3 + operation_milestones.days)) then 1 else 0 end) as critical2_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.stalled > operation_milestones.days)  and (operation_metrics.stalled >= (operation_milestones.critical3 + operation_milestones.days)) and (operation_metrics.stalled < (operation_milestones.critical4 + operation_milestones.days)) then 1 else 0 end) as critical3_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.stalled > operation_milestones.days)  and (operation_metrics.stalled >= (operation_milestones.critical4 + operation_milestones.days)) then 1 else 0 end) as critical4_count"))
            ->addSelect("operation_batches.updated_at as batch_date")
            ->addSelect("operation_batches.id as batch_id")
            ->distinct()
            ->join('operation_batches', 'operation_batches.panel_id', '=', 'operation_panels.id')
            ->join('operation_metrics', 'operation_metrics.batch_id', '=', 'operation_batches.id')
            ->join('operation_milestones', 'operation_metrics.milestone_id', '=', 'operation_milestones.id')
            ->whereNotNull('operation_batches.panel_id')
            ->where('operation_batches.total', '>', 0)
            ->whereColumn('operation_batches.processed', '>=', 'operation_batches.total')
            ->groupBy('operation_panels.id')
            ->orderBy('operation_panels.id')
            ->orderByDesc('operation_batches.created_at')
            ->get();
    }

    public function addSegment(Panel $panel, $attributes = [])
    {
        return $panel->segments()->create($attributes);
    }
}