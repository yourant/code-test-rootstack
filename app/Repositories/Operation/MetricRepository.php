<?php

namespace App\Repositories\Operation;

use App\Models\Operation\Batch;
use App\Models\Operation\Milestone;
use App\Models\Operation\Panel;
use App\Models\Operation\Segment;
use App\Repositories\AbstractRepository;
use App\Models\Operation\Metric;
use DB;
use Illuminate\Support\Collection;

class MetricRepository extends AbstractRepository
{
    function __construct(Metric $model)
    {
        $this->model = $model;
    }

    public function getAlertsGroupedByMilestone(Segment $segment, Batch $batch)
    {
        $query = $this->model
            ->select([
                'operation_milestones.segment_id',
                'operation_metrics.milestone_id',
                'operation_milestones.name',
                'operation_milestones.days',
                'operation_milestones.accumulated_days',
                'operation_milestones.warning1',
                'operation_milestones.warning2',
                'operation_milestones.critical1',
                'operation_milestones.critical2',
                'operation_milestones.critical3',
                'operation_milestones.critical4',
            ])
            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled < (operation_milestones.accumulated_days - operation_milestones.warning2)) then 1 else 0 end ) as warning1_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled < operation_milestones.accumulated_days and operation_metrics.controlled >= (operation_milestones.accumulated_days - operation_milestones.warning2)) then 1 else 0 end ) as warning2_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled >= (operation_milestones.accumulated_days + 0) and operation_metrics.controlled < (operation_milestones.accumulated_days + operation_milestones.critical2)) then 1 else 0 end ) as critical1_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled >= (operation_milestones.accumulated_days + operation_milestones.critical2) and operation_metrics.controlled < (operation_milestones.accumulated_days + operation_milestones.critical3)) then 1 else 0 end ) as critical2_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled >= (operation_milestones.accumulated_days + operation_milestones.critical3) and operation_metrics.controlled < (operation_milestones.accumulated_days + operation_milestones.critical4)) then 1 else 0 end ) as critical3_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled >= (operation_milestones.accumulated_days + operation_milestones.critical4) ) then 1 else 0 end ) as critical4_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.segment > operation_segments.duration) then 1 else 0 end ) as segment_expired_count"))
//            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled > agreements.controlled_transit_days) then 1 else 0 end ) as controlled_expired_count"))
//            ->addSelect(DB::raw("sum(case when ((operation_metrics.controlled >= agreements.controlled_transit_days) and packages.clockstop = 0 and packages.stalled = 0) then 1 else 0 end ) as controlled_expired_count"))
            ->addSelect(DB::raw("sum(case when ((operation_metrics.controlled > delivery_routes.controlled_transit_days) and packages.clockstop = 0 and packages.stalled = 0) then 1 else 0 end ) as controlled_expired_count"))
            ->addSelect(DB::raw("count(operation_metrics.package_id) as package_count"))
            ->join('operation_milestones', 'operation_metrics.milestone_id', '=', 'operation_milestones.id')
            ->join('operation_segments', 'operation_milestones.segment_id', '=', 'operation_segments.id')
            ->join('packages', 'operation_metrics.package_id', '=', 'packages.id')
//            ->join('agreements', 'packages.agreement_id', '=', 'agreements.id')
            ->join('delivery_routes', 'packages.delivery_route_id', '=', 'delivery_routes.id')
            ->ofBatchId($batch->id)
            ->where('operation_milestones.segment_id', $segment->id)
            ->groupBy([
                'operation_milestones.segment_id',
                'operation_metrics.milestone_id',
                'operation_milestones.name',
                'operation_milestones.days',
                'operation_milestones.accumulated_days',
                'operation_milestones.warning1',
                'operation_milestones.warning2',
                'operation_milestones.critical1',
                'operation_milestones.critical2',
                'operation_milestones.critical3',
                'operation_milestones.critical4',
                'operation_milestones.position'
            ])
            ->orderBy('operation_milestones.position');

        return $query->get();
    }

    public function getAlertsGroupedBySegment(Panel $panel, Batch $batch)
    {
        $query = $this->model
            ->select([
                'operation_milestones.segment_id',
                'operation_milestones.days',
                'operation_milestones.accumulated_days',
                'operation_milestones.warning1',
                'operation_milestones.warning2',
                'operation_milestones.critical1',
                'operation_milestones.critical2',
                'operation_milestones.critical3',
                'operation_milestones.critical4',
            ])
            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled < (operation_milestones.accumulated_days - operation_milestones.warning2)) then 1 else 0 end ) as warning1_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled < operation_milestones.accumulated_days and operation_metrics.controlled >= (operation_milestones.accumulated_days - operation_milestones.warning2)) then 1 else 0 end ) as warning2_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled >= (operation_milestones.accumulated_days + 0) and operation_metrics.controlled < (operation_milestones.accumulated_days + operation_milestones.critical2)) then 1 else 0 end ) as critical1_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled >= (operation_milestones.accumulated_days + operation_milestones.critical2) and operation_metrics.controlled < (operation_milestones.accumulated_days + operation_milestones.critical3)) then 1 else 0 end ) as critical2_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled >= (operation_milestones.accumulated_days + operation_milestones.critical3) and operation_metrics.controlled < (operation_milestones.accumulated_days + operation_milestones.critical4)) then 1 else 0 end ) as critical3_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled >= (operation_milestones.accumulated_days + operation_milestones.critical4) ) then 1 else 0 end ) as critical4_count"))
//            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled >= agreements.controlled_transit_days) then 1 else 0 end ) as expired_count"))
//            ->addSelect(DB::raw("sum(case when ((operation_metrics.controlled >= agreements.controlled_transit_days) and packages.clockstop = 0 and packages.stalled = 0) then 1 else 0 end ) as expired_count"))
            ->addSelect(DB::raw("sum(case when ((operation_metrics.controlled > delivery_routes.controlled_transit_days) and packages.clockstop = 0 and packages.stalled = 0) then 1 else 0 end ) as expired_count"))
//            ->addSelect(DB::raw("sum(case when ((agreements.controlled_transit_days - operation_metrics.controlled) BETWEEN 0 and 3) then 1 else 0 end ) as close_to_expire"))
//            ->addSelect(DB::raw("sum(case when (((agreements.controlled_transit_days - operation_metrics.controlled) BETWEEN 0 and 3) and packages.clockstop = 0 and packages.stalled = 0) then 1 else 0 end ) as close_to_expire"))
            ->addSelect(DB::raw("sum(case when (((delivery_routes.controlled_transit_days - operation_metrics.controlled) BETWEEN 0 and 3) and packages.clockstop = 0 and packages.stalled = 0) then 1 else 0 end ) as close_to_expire"))
            ->addSelect(DB::raw("count(operation_metrics.package_id) as package_count"))
            ->addSelect(DB::raw("sum(case when (packages.first_controlled_checkpoint_at is not null) then 1 else 0 end) as controlled_package_count"))
            ->join('operation_milestones', 'operation_metrics.milestone_id', '=', 'operation_milestones.id')
            ->join('operation_segments', 'operation_milestones.segment_id', '=', 'operation_segments.id')
            ->join('packages', 'operation_metrics.package_id', '=', 'packages.id')
//            ->join('agreements', 'packages.agreement_id', '=', 'agreements.id')
            ->join('delivery_routes', 'packages.delivery_route_id', '=', 'delivery_routes.id')
            ->where('operation_segments.panel_id', $panel->id)
            ->ofBatchId($batch->id)
            ->groupBy([
                'operation_milestones.segment_id',
                'operation_milestones.days',
                'operation_milestones.accumulated_days',
                'operation_milestones.warning1',
                'operation_milestones.warning2',
                'operation_milestones.critical1',
                'operation_milestones.critical2',
                'operation_milestones.critical3',
                'operation_milestones.critical4','operation_segments.position'])
            ->orderBy('operation_segments.position');

        return $query->get();
    }

    public function getPickAndPackAlertsGroupedByMilestone(Segment $segment, Batch $batch)
    {
        $query = $this->model
            ->select([
                'operation_milestones.segment_id',
                'operation_metrics.milestone_id',
                'operation_milestones.name',
                'operation_milestones.days',
            ])
            ->addSelect(DB::raw("sum(case when (operation_metrics.segment > operation_segments.duration) then 1 else 0 end ) as segment_expired_count"))
            ->addSelect(DB::raw("count(operation_metrics.package_id) as package_count"))
            ->join('operation_milestones', 'operation_metrics.milestone_id', '=', 'operation_milestones.id')
            ->join('operation_segments', 'operation_milestones.segment_id', '=', 'operation_segments.id')
            ->ofBatchId($batch->id)
            ->where('operation_milestones.segment_id', $segment->id)
            ->groupBy([
                'operation_milestones.segment_id',
                'operation_metrics.milestone_id',
                'operation_milestones.name',
                'operation_milestones.days',
                'operation_milestones.position'
            ])
            ->orderBy('operation_milestones.position');

        return $query->get();
    }

    public function getAlerts(Panel $panel, Batch $batch)
    {
        $query = $this->model
            ->select('operation_metrics.batch_id')
            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled < (operation_milestones.accumulated_days - operation_milestones.warning2)) then 1 else 0 end ) as warning1_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled < operation_milestones.accumulated_days and operation_metrics.controlled >= (operation_milestones.accumulated_days - operation_milestones.warning2)) then 1 else 0 end ) as warning2_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled >= (operation_milestones.accumulated_days + 0) and operation_metrics.controlled < (operation_milestones.accumulated_days + operation_milestones.critical2)) then 1 else 0 end ) as critical1_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled >= (operation_milestones.accumulated_days + operation_milestones.critical2) and operation_metrics.controlled < (operation_milestones.accumulated_days + operation_milestones.critical3)) then 1 else 0 end ) as critical2_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled >= (operation_milestones.accumulated_days + operation_milestones.critical3) and operation_metrics.controlled < (operation_milestones.accumulated_days + operation_milestones.critical4)) then 1 else 0 end ) as critical3_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled >= (operation_milestones.accumulated_days + operation_milestones.critical4) ) then 1 else 0 end ) as critical4_count"))
//            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled >= agreements.controlled_transit_days) then 1 else 0 end ) as expired_count"))
//            ->addSelect(DB::raw("sum(case when ((operation_metrics.controlled >= agreements.controlled_transit_days) and packages.clockstop = 0 and packages.stalled = 0) then 1 else 0 end ) as expired_count"))
            ->addSelect(DB::raw("sum(case when ((operation_metrics.controlled > delivery_routes.controlled_transit_days) and packages.clockstop = 0 and packages.stalled = 0) then 1 else 0 end ) as expired_count"))
            ->addSelect(DB::raw("count(operation_metrics.package_id) as package_count"))
            ->addSelect(DB::raw("sum(case when (packages.first_controlled_checkpoint_at is not null) then 1 else 0 end) as controlled_package_count"))
            ->join('operation_batches', 'operation_metrics.batch_id', '=', 'operation_batches.id')
            ->join('operation_milestones', 'operation_metrics.milestone_id', '=', 'operation_milestones.id')
            ->join('operation_segments', 'operation_milestones.segment_id', '=', 'operation_segments.id')
            ->join('packages', 'operation_metrics.package_id', '=', 'packages.id')
//            ->join('agreements', 'packages.agreement_id', '=', 'agreements.id')
            ->join('delivery_routes', 'packages.delivery_route_id', '=', 'delivery_routes.id')
            ->where('operation_segments.panel_id', $panel->id)
            ->ofBatchId($batch->id)
            ->groupBy('operation_metrics.batch_id');

        return $query->first();
    }

    public function searchByMilestoneAndAlert(Milestone $milestone = null, Segment $segment = null, $alert = [], Batch $batch, $params = [])
    {
        $joins = collect();
        $filters = collect($params);
        $alerts = collect($alert);

        $query = $this->model
            ->select('operation_metrics.*')
            ->join('packages', 'operation_metrics.package_id', '=', 'packages.id');

        $this->addJoin($joins, 'operation_milestones', 'operation_metrics.milestone_id', 'operation_milestones.id');

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

        if ($filters->has('last_checkpoint_code_id')) {
            $this->addJoin($joins, 'checkpoints as last_checkpoints', 'last_checkpoints.id', 'packages.last_checkpoint_id');
            $lcc_id = $filters->get('last_checkpoint_code_id');
            if (is_array($lcc_id) && !empty($lcc_id)) {
                $query->whereIn('last_checkpoints.checkpoint_code_id', $lcc_id);
            } else {
                $query->where('last_checkpoints.checkpoint_code_id', $lcc_id);
            }
        }

        // Conditions by alert type
        $query->where(function ($subquery) use ($alerts, $joins) {
            if ($alerts->contains('warning1')) {
                $subquery->orWhereRaw(DB::raw("(operation_metrics.controlled < (operation_milestones.accumulated_days - operation_milestones.warning2))"));
            }
            if ($alerts->contains('warning2')) {
                $subquery->orWhereRaw(DB::raw("(operation_metrics.controlled < operation_milestones.accumulated_days and operation_metrics.controlled >= (operation_milestones.accumulated_days - operation_milestones.warning2))"));
            }
            if ($alerts->contains('critical1')) {
                $subquery->orWhereRaw(DB::raw("(operation_metrics.controlled >= (operation_milestones.accumulated_days + 0) and operation_metrics.controlled < (operation_milestones.accumulated_days + operation_milestones.critical2))"));
            }
            if ($alerts->contains('critical2')) {
                $subquery->orWhereRaw(DB::raw("(operation_metrics.controlled >= (operation_milestones.accumulated_days + operation_milestones.critical2) and operation_metrics.controlled < (operation_milestones.accumulated_days + operation_milestones.critical3))"));
            }
            if ($alerts->contains('critical3')) {
                $subquery->orWhereRaw(DB::raw("(operation_metrics.controlled >= (operation_milestones.accumulated_days + operation_milestones.critical3) and operation_metrics.controlled < (operation_milestones.accumulated_days + operation_milestones.critical4))"));
            }
            if ($alerts->contains('critical4')) {
                $subquery->orWhereRaw(DB::raw("(operation_metrics.controlled >= (operation_milestones.accumulated_days + operation_milestones.critical4))"));
            }
            if ($alerts->contains('expired_segment')) {
                $this->addJoin($joins, 'operation_segments', 'operation_milestones.segment_id', 'operation_segments.id');
                $subquery->orWhereRaw(DB::raw("(operation_metrics.segment > operation_segments.duration)"));
            }
            if ($alerts->contains('expired_controlled')) {
//                $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
                $this->addJoin($joins, 'delivery_routes', 'packages.delivery_route_id', 'delivery_routes.id');
                $subquery->orWhereRaw(DB::raw("((operation_metrics.controlled > delivery_routes.controlled_transit_days) and packages.clockstop = 0 and packages.stalled = 0)"));
            }
            if ($alerts->contains('close_to_expire')) {
//                $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
                $this->addJoin($joins, 'delivery_routes', 'packages.delivery_route_id', 'delivery_routes.id');
                $subquery->orWhereRaw(DB::raw("(((delivery_routes.controlled_transit_days - operation_metrics.controlled) BETWEEN 0 and 3) and packages.clockstop = 0 and packages.stalled = 0)"));
            }
            if ($alerts->contains('controlled')) {
                $subquery->orWhereNotNull('packages.first_controlled_checkpoint_at');
            }

            return $subquery;
        });

        if ($alerts->contains('worst20')) {
            $query->where('operation_metrics.controlled', '>=', 0)
                ->orderBy('operation_metrics.controlled', 'desc')
                ->limit(20);
        }

        // Additional conditions
        if ($milestone) {
            $query->where('operation_metrics.milestone_id', $milestone->id);
        } elseif ($segment) {
            $query->where('operation_milestones.segment_id', $segment->id);
        }

        $query->ofBatchId($batch->id);

        // Apply joins
        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        // Sorting
        if (isset($params['sort_by']) && $params['sort_by']) {
            $column = $params['sort_by'];
            $direction = 'asc';
            if (isset($params['sort_direction']) && $params['sort_direction']) {
                $direction = $params['sort_direction'];
            }

            $query->orderBy('operation_metrics.' . $column, $direction);
        } else {
            $query->orderBy('operation_metrics.controlled', 'desc');
        }

        return $query;
    }

    public function getOldestControlled(Batch $batch)
    {
        return $this->model
            ->select('operation_metrics.*')
            ->ofBatchId($batch->id)
            ->where('controlled', '>=', 0)
            ->orderBy('operation_metrics.controlled', 'desc')
            ->limit(1)
            ->first();
    }

    public function getArchiveSummaryByBatch(Batch $batch)
    {
        $query = $this->model
            ->select('operation_metrics.batch_id')
            ->addSelect('operation_milestones.segment_id')
            ->addSelect(DB::raw("count(operation_metrics.package_id) as package_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.segment > operation_segments.duration) then 1 else 0 end ) as segment_count"))
//            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled > agreements.controlled_transit_days) then 1 else 0 end ) as critical_count"))
            ->addSelect(DB::raw("sum(case when (operation_metrics.controlled > delivery_routes.controlled_transit_days) then 1 else 0 end ) as critical_count"))
            ->addSelect(DB::raw("avg(operation_metrics.stalled) as stalled"))
            ->addSelect(DB::raw("avg(operation_metrics.controlled) as controlled"))
            ->addSelect(DB::raw("avg(operation_metrics.total) as total"))
            ->join('operation_batches', 'operation_metrics.batch_id', '=', 'operation_batches.id')
            ->join('operation_milestones', 'operation_metrics.milestone_id', '=', 'operation_milestones.id')
            ->join('operation_segments', 'operation_milestones.segment_id', '=', 'operation_segments.id')
            ->join('packages', 'operation_metrics.package_id', '=', 'packages.id')
//            ->join('agreements', 'packages.agreement_id', '=', 'agreements.id')
            ->join('delivery_routes', 'packages.delivery_route_id', '=', 'delivery_routes.id')
            ->ofBatchId($batch->id)
            ->groupBy(['operation_metrics.batch_id', 'operation_milestones.segment_id']);

        return $query->get();
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }
}
