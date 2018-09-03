<?php

namespace App\Repositories\Operation;

use App\Models\Operation\Batch;
use App\Models\Operation\Milestone;
use App\Models\Operation\StateMilestone;
use App\Models\Operation\StateMilestoneMetric;
use App\Repositories\AbstractRepository;
use DB;
use Illuminate\Support\Collection;

class StateMilestoneMetricRepository extends AbstractRepository
{
    function __construct(StateMilestoneMetric $model)
    {
        $this->model = $model;
    }

    public function getGroupedByState(Milestone $milestone, Batch $batch, array $filters = [])
    {
        $filters = collect($filters);

        $query = $this->model
            ->select([
                'operation_state_milestones.milestone_id',
                'operation_state_milestone_metrics.state_milestone_id',
                'admin_level_1.name',
                'regions.name as region',
                'operation_state_milestones.days',
                'operation_state_milestones.accumulated_days',
                'operation_state_milestones.warning1',
                'operation_state_milestones.warning2',
                'operation_state_milestones.critical1',
                'operation_state_milestones.critical2',
                'operation_state_milestones.critical3',
                'operation_state_milestones.critical4',
            ])
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled < (operation_state_milestones.accumulated_days - operation_state_milestones.warning2)) then 1 else 0 end ) as warning1_count"))
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled < operation_state_milestones.accumulated_days and operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days - operation_state_milestones.warning2)) then 1 else 0 end ) as warning2_count"))
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days + 0) and operation_state_milestone_metrics.controlled < (operation_state_milestones.accumulated_days + operation_state_milestones.critical2)) then 1 else 0 end ) as critical1_count"))
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days + operation_state_milestones.critical2) and operation_state_milestone_metrics.controlled < (operation_state_milestones.accumulated_days + operation_state_milestones.critical3)) then 1 else 0 end ) as critical2_count"))
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days + operation_state_milestones.critical3) and operation_state_milestone_metrics.controlled < (operation_state_milestones.accumulated_days + operation_state_milestones.critical4)) then 1 else 0 end ) as critical3_count"))
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days + operation_state_milestones.critical4) ) then 1 else 0 end ) as critical4_count"))
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled >= (operation_state_milestones.critical1 + operation_state_milestones.accumulated_days)) then 1 else 0 end) as critical_count"))
//            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled >= agreements.controlled_transit_days) then 1 else 0 end ) as expired_count"))
            ->addSelect(DB::raw("sum(case when ((operation_state_milestone_metrics.controlled > delivery_routes.controlled_transit_days) and packages.clockstop = 0 and packages.stalled = 0) then 1 else 0 end ) as expired_count"))
            ->addSelect(DB::raw("count(operation_state_milestone_metrics.package_id) as package_count"))
            ->join('operation_state_milestones', 'operation_state_milestone_metrics.state_milestone_id', '=', 'operation_state_milestones.id')
            ->join('admin_level_1', 'operation_state_milestones.admin_level_1_id', '=', 'admin_level_1.id', 'left outer')
            ->join('regions', 'admin_level_1.region_id', '=', 'regions.id', 'left outer')
            ->join('packages', 'operation_state_milestone_metrics.package_id', '=', 'packages.id')
            ->join('delivery_routes', 'packages.delivery_route_id', '=', 'delivery_routes.id')
            ->ofBatchId($batch->id)
            ->where('operation_state_milestones.milestone_id', $milestone->id)
            ->groupBy(['operation_state_milestones.milestone_id', 'operation_state_milestone_metrics.state_milestone_id',
                'admin_level_1.name',
                'regions.name',
                'operation_state_milestones.days',
                'operation_state_milestones.accumulated_days',
                'operation_state_milestones.warning1',
                'operation_state_milestones.warning2',
                'operation_state_milestones.critical1',
                'operation_state_milestones.critical2',
                'operation_state_milestones.critical3',
                'operation_state_milestones.critical4'])
            ->orderBy('critical_count', 'desc')
            ->orderBy('admin_level_1.name');

        if ($region_id = $filters->get('region_id')) {
            if (is_array($region_id) && !empty($region_id)) {
                $query->whereIn('regions.id', $region_id);
            } else {
                $query->where('regions.id', $region_id);
            }
        }

        if ($admin_level_1_id = $filters->get('admin_level_1_id')) {
            if (is_array($admin_level_1_id) && !empty($admin_level_1_id)) {
                $query->whereIn('admin_level_1.id', $admin_level_1_id);
            } else {
                $query->where('admin_level_1.id', $admin_level_1_id);
            }
        }

        return $query->get();
    }

    public function getCriticalByMilestone(Milestone $milestone, Batch $batch)
    {
        $panel = $milestone->getSegmentPanel();

        $query = $this->model
            ->select([
                'operation_milestones.id',
                'operation_state_milestones.admin_level_1_id',
                'admin_level_1.name as state',
                'regions.name as region',
                'operation_milestones.days',
                'operation_milestones.warning1',
                'operation_milestones.warning2',
                'operation_milestones.critical1',
                'operation_milestones.critical2',
                'operation_milestones.critical3',
                'operation_milestones.critical4',
            ])
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled < (operation_state_milestones.accumulated_days - operation_state_milestones.warning2)) then 1 else 0 end ) as warning1_count"))
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled < operation_state_milestones.accumulated_days and operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days - operation_state_milestones.warning2)) then 1 else 0 end ) as warning2_count"))
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days + 0) and operation_state_milestone_metrics.controlled < (operation_state_milestones.accumulated_days + operation_state_milestones.critical2)) then 1 else 0 end ) as critical1_count"))
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days + operation_state_milestones.critical2) and operation_state_milestone_metrics.controlled < (operation_state_milestones.accumulated_days + operation_state_milestones.critical3)) then 1 else 0 end ) as critical2_count"))
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days + operation_state_milestones.critical3) and operation_state_milestone_metrics.controlled < (operation_state_milestones.accumulated_days + operation_state_milestones.critical4)) then 1 else 0 end ) as critical3_count"))
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days + operation_state_milestones.critical4) ) then 1 else 0 end ) as critical4_count"))
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled >= (operation_state_milestones.critical1 + operation_state_milestones.accumulated_days)) then 1 else 0 end) as critical_count"))
            ->addSelect(DB::raw("count(operation_state_milestone_metrics.package_id) as package_count"))
            ->join('operation_state_milestones', 'operation_state_milestone_metrics.state_milestone_id', '=', 'operation_state_milestones.id')
            ->join('operation_milestones', 'operation_state_milestones.milestone_id', '=', 'operation_milestones.id')
            ->join('operation_segments', 'operation_milestones.segment_id', '=', 'operation_segments.id')
            ->join('admin_level_1', 'operation_state_milestones.admin_level_1_id', '=', 'admin_level_1.id')
            ->join('regions', 'admin_level_1.region_id', '=', 'regions.id', 'left outer')
            ->where('operation_segments.panel_id', $panel->id)
            ->where('operation_milestones.id', $milestone->id)
            ->ofBatchId($batch->id)
            ->groupBy(['operation_milestones.id', 'operation_state_milestones.admin_level_1_id'])
            ->orderBy('critical_count', 'desc');

        return $query->get();
    }

    public function getCriticalSummaryByMilestone(Milestone $milestone, Batch $batch)
    {
        $panel = $milestone->getSegmentPanel();

        $query = $this->model
            ->select([
                'operation_milestones.id',
                'operation_state_milestones.admin_level_1_id',
                'admin_level_1.name as state',
                'operation_state_milestones.id as state_milestone_id',
                'operation_state_milestones.critical4',
                'operation_state_milestones.critical3',
                'operation_state_milestones.critical2',
                'operation_state_milestones.critical1',
                'operation_state_milestones.warning2',
                'operation_state_milestones.warning1',
                'operation_milestones.days',
                'operation_state_milestones.accumulated_days',
            ])
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled < (operation_state_milestones.accumulated_days - operation_state_milestones.warning2)) then 1 else 0 end ) as warning1_count"))
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled < operation_state_milestones.accumulated_days and operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days - operation_state_milestones.warning2)) then 1 else 0 end ) as warning2_count"))
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days + 0) and operation_state_milestone_metrics.controlled < (operation_state_milestones.accumulated_days + operation_state_milestones.critical2)) then 1 else 0 end ) as critical1_count"))
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days + operation_state_milestones.critical2) and operation_state_milestone_metrics.controlled < (operation_state_milestones.accumulated_days + operation_state_milestones.critical3)) then 1 else 0 end ) as critical2_count"))
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days + operation_state_milestones.critical3) and operation_state_milestone_metrics.controlled < (operation_state_milestones.accumulated_days + operation_state_milestones.critical4)) then 1 else 0 end ) as critical3_count"))
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days + operation_state_milestones.critical4) ) then 1 else 0 end ) as critical4_count"))
            ->addSelect(DB::raw("sum(case when (operation_state_milestone_metrics.controlled >= (operation_state_milestones.critical1 + operation_state_milestones.accumulated_days)) then 1 else 0 end) as critical_count"))
            ->addSelect(DB::raw("count(operation_state_milestone_metrics.package_id) as package_count"))
            ->join('operation_state_milestones', 'operation_state_milestone_metrics.state_milestone_id', '=', 'operation_state_milestones.id')
            ->join('operation_milestones', 'operation_state_milestones.milestone_id', '=', 'operation_milestones.id')
            ->join('operation_segments', 'operation_milestones.segment_id', '=', 'operation_segments.id')
            ->join('admin_level_1', 'operation_state_milestones.admin_level_1_id', '=', 'admin_level_1.id', 'left outer')
            ->where('operation_segments.panel_id', $panel->id)
            ->where('operation_milestones.id', $milestone->id)
            ->ofBatchId($batch->id)
            ->groupBy([
                'operation_milestones.id',
                'operation_state_milestones.id',
                'admin_level_1.name'
            ])
            ->orderBy('critical_count', 'desc');

        return $query->get();
    }

    public function searchByStateMilestoneAndAlert($stateMilestone = null, $milestone, $alert = [], Batch $batch, $params = [])
    {
        $joins = collect();
        $filters = collect($params);
        $alerts = collect($alert);

        $query = $this->model
            ->select('operation_state_milestone_metrics.*')
            ->join('operation_state_milestones', 'operation_state_milestone_metrics.state_milestone_id', '=', 'operation_state_milestones.id')
            ->join('packages', 'operation_state_milestone_metrics.package_id', '=', 'packages.id');

        if (isset($params['tracking']) && $params['tracking']) {
            $query->where('packages.tracking_number', strtoupper($params['tracking']));
        }

        if (isset($params['first_checkpoint_newer_than']) && $params['first_checkpoint_newer_than']) {
            $query->where('packages.first_checkpoint_at', '>=', $params['first_checkpoint_newer_than'] . ' 00:00:00');
        }

        if (isset($params['first_checkpoint_older_than']) && $params['first_checkpoint_older_than']) {
            $query->where('packages.first_checkpoint_at', '<=', $params['first_checkpoint_older_than'] . ' 23:59:59');
        }

        if (isset($params['cn38_greater_than']) && $params['cn38_greater_than']) {
            $this->addJoin($joins, 'bags', 'packages.bag_id', 'bags.id');
            $this->addJoin($joins, 'dispatches', 'bags.dispatch_id', 'dispatches.id');
            $query->where('dispatches.code', '>=', $params['cn38_greater_than']);
        }

        if (isset($params['cn38_lower_than']) && $params['cn38_lower_than']) {
            $this->addJoin($joins, 'bags', 'packages.bag_id', 'bags.id');
            $this->addJoin($joins, 'dispatches', 'bags.dispatch_id', 'dispatches.id');
            $query->where('dispatches.code', '<=', $params['cn38_lower_than']);
        }

        if ($filters->has('client_id')) {
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'clients', 'agreements.client_id', 'clients.id');

            $client_id = $filters->get('client_id');
            if (is_array($client_id) && !empty($client_id)) {
                $query->whereIn('agreements.client_id', $client_id);
            } else {
                $query->where('agreements.client_id', $client_id);
            }
        }

        // Filter by state
        if ($state_id = $filters->get('state_id')) {
            $this->addJoin($joins, 'admin_level_1', 'operation_state_milestones.admin_level_1_id', 'admin_level_1.id');
            $this->addJoin($joins, 'regions', 'admin_level_1.region_id', 'regions.id');

            if (is_array($state_id) && !empty($state_id)) {
                $query->whereIn('admin_level_1.id', $state_id);
            } else {
                $query->where('admin_level_1.id', $state_id);
            }
        }

        // Filter by region
        if ($region_id = $filters->get('region_id')) {
            $this->addJoin($joins, 'admin_level_1', 'operation_state_milestones.admin_level_1_id', 'admin_level_1.id');
            $this->addJoin($joins, 'regions', 'admin_level_1.region_id', 'regions.id');
            if (is_array($region_id) && !empty($region_id)) {
                $query->whereIn('regions.id', $region_id);
            } else {
                $query->where('regions.id', $region_id);
            }
        }


        if ($filters->has('last_checkpoint_code_id')) {
            $this->addJoin($joins, 'checkpoints as last_checkpoints', 'last_checkpoints.id', 'packages.last_checkpoint_id');
            $lcc_id = $filters->get('last_checkpoint_code_id');
            if (is_array($lcc_id) && !empty($lcc_id)) {
                $query->whereIn('last_checkpoints.checkpoint_code_id', $lcc_id);
            } else {
                $query->where('last_checkpoints.checkpoint_code_id', $lcc_id);
            }
        }

        if ($milestone) {
            $this->addJoin($joins, 'operation_milestones', 'operation_state_milestones.milestone_id', 'operation_milestones.id');
            $query->where('operation_milestones.id', $milestone->id);
        }


        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        // Conditions by alert type
        $query->where(function ($subquery) use ($alerts) {
            if ($alerts->contains('warning1')) {
                $subquery->orWhereRaw(DB::raw("(operation_state_milestone_metrics.controlled < (operation_state_milestones.accumulated_days - operation_state_milestones.warning2))"));
            }
            if ($alerts->contains('warning2')) {
                $subquery->orWhereRaw(DB::raw("(operation_state_milestone_metrics.controlled < operation_state_milestones.accumulated_days and operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days - operation_state_milestones.warning2))"));
            }
            if ($alerts->contains('critical1')) {
                $subquery->orWhereRaw(DB::raw("(operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days + 0) and operation_state_milestone_metrics.controlled < (operation_state_milestones.accumulated_days + operation_state_milestones.critical2))"));
            }
            if ($alerts->contains('critical2')) {
                $subquery->orWhereRaw(DB::raw("(operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days + operation_state_milestones.critical2) and operation_state_milestone_metrics.controlled < (operation_state_milestones.accumulated_days + operation_state_milestones.critical3))"));
            }
            if ($alerts->contains('critical3')) {
                $subquery->orWhereRaw(DB::raw("(operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days + operation_state_milestones.critical3) and operation_state_milestone_metrics.controlled < (operation_state_milestones.accumulated_days + operation_state_milestones.critical4))"));
            }
            if ($alerts->contains('critical4')) {
                $subquery->orWhereRaw(DB::raw("(operation_state_milestone_metrics.controlled >= (operation_state_milestones.accumulated_days + operation_state_milestones.critical4))"));
            }
            return $subquery;
        });
        
        // Additional conditions
        $query->ofBatchId($batch->id);

        if ($stateMilestone) {
            if (is_array($stateMilestone)) {
                $query->ofStateMilestoneId($stateMilestone);
            } else {
                $query->ofStateMilestoneId($stateMilestone->id);
            }
        }

        // Sorting
        if (isset($params['sort_by']) && $params['sort_by']) {
            $column = $params['sort_by'];
            $direction = 'asc';
            if (isset($params['sort_direction']) && $params['sort_direction']) {
                $direction = $params['sort_direction'];
            }

            $query->orderBy('operation_state_milestone_metrics.' . $column, $direction);
        } else {
            $query->orderBy('operation_state_milestone_metrics.controlled', 'desc');
        }

        return $query;
    }

    public function searchByMilestone(Milestone $milestone, Batch $batch, $params = [])
    {
        $joins = collect();
        $filters = collect($params);

        $query = $this->model
            ->select('operation_state_milestone_metrics.*')
            ->join('operation_state_milestones', 'operation_state_milestone_metrics.state_milestone_id', '=', 'operation_state_milestones.id')
            ->join('packages', 'operation_state_milestone_metrics.package_id', '=', 'packages.id');

        // Filter by region 
        if ($region_id = $filters->get('region_id')) {
            $this->addJoin($joins, 'admin_level_1', 'operation_state_milestones.admin_level_1_id', 'admin_level_1.id');
            $this->addJoin($joins, 'regions', 'admin_level_1.region_id', 'regions.id');
            if (is_array($region_id) && !empty($region_id)) {
                $query->whereIn('regions.id', $region_id);
            } else {
                $query->where('regions.id', $region_id);
            }
        }

        // Filter by state
        if ($admin_level_1_id = $filters->get('admin_level_1_id')) {
            $this->addJoin($joins, 'admin_level_1', 'operation_state_milestones.admin_level_1_id', 'admin_level_1.id');
            $this->addJoin($joins, 'regions', 'admin_level_1.region_id', 'regions.id');

            if (is_array($admin_level_1_id) && !empty($admin_level_1_id)) {
                $query->whereIn('admin_level_1.id', $admin_level_1_id);
            } else {
                $query->where('admin_level_1.id', $admin_level_1_id);
            }
        }

        // Perform joins
        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        // Additional conditions
        $query->where('operation_state_milestones.milestone_id', $milestone->id)->ofBatchId($batch->id);

        // Sorting
        $query->orderBy('operation_state_milestone_metrics.stalled', 'desc');

        return $query;
    }

    public function getMaxStalled(Batch $batch)
    {
        return $this->model
            ->select('operation_state_milestone_metrics.*')
            ->ofBatchId($batch->id)
//            ->where('controlled', '>', 0)
            ->orderBy('operation_state_milestone_metrics.stalled', 'desc')
            ->limit(1)
            ->first();
    }

    public function getArchiveSummaryByBatch(Batch $batch)
    {
        $query = $this->model
            ->select('operation_state_milestone_metrics.batch_id')
            ->addSelect('operation_state_milestone_metrics.state_milestone_id')
            ->addSelect(DB::raw("count(operation_state_milestone_metrics.package_id) as package_count"))
            ->join('operation_batches', 'operation_state_milestone_metrics.batch_id', '=', 'operation_batches.id')
            ->ofBatchId($batch->id)
            ->groupBy(['operation_state_milestone_metrics.batch_id', 'operation_state_milestone_metrics.state_milestone_id']);

        return $query->get();
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }
}