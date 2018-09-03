<?php

namespace App\Repositories\Operation;

use App\Repositories\AbstractRepository;
use App\Models\Operation\Segment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SegmentRepository extends AbstractRepository
{
    function __construct(Segment $model)
    {
        $this->model = $model;
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }

    public function addMilestone(Segment $segment, $attributes = [])
    {
        return $segment->milestones()->create($attributes);
    }

    public function addStateMilestone(Segment $segment, $attributes = [])
    {
        return $segment->stateMilestones()->create($attributes);
    }

    public function getOperationsSegmentQuery(Segment $segment, $params)
    {
        // Add Selects
        $now = Carbon::now()->toDateTimeString();
        $query = $this->model
            ->select('countries.id as country_id')
            ->addSelect('countries.name as country_name')
//            ->addSelect('agreements.type as service_type')
            ->addSelect(DB::raw("count(packages.id) as total"));

        $joins = collect();
        $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
        $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
        $this->addJoin($joins, 'locations as destination_location', 'services.destination_location_id', 'destination_location.id');
        $this->addJoin($joins, 'countries', 'destination_location.country_id', 'countries.id');
        $this->addJoin($joins, 'checkpoints as last_checkpoints', 'packages.last_checkpoint_id', 'last_checkpoints.id');

        $checkpointCodes = collect();
        foreach ($segment->milestones as $milestone) {
            foreach ($milestone->checkpointCodes as $checkpointCode) {
                $checkpointCodes->push($checkpointCode->id);
            }
        }

        $query->whereIn('last_checkpoints.checkpoint_code_id', $checkpointCodes->toArray());

        foreach ($segment->boundaries as $boundary) {
            if ($boundary->upper) {
                $query->addSelect(DB::raw("sum(case when (date_part('day','" . $now . "' - packages.last_checkpoint_at) >= {$boundary->lower}) and date_part('day','" . $now . "' - packages.last_checkpoint_at) < {$boundary->upper} then 1 else 0 end) as boundary_{$boundary->id}"));
            } else {
                $query->addSelect(DB::raw("sum(case when (date_part('day','" . $now . "' - packages.last_checkpoint_at) >= {$boundary->lower}) then 1 else 0 end) as boundary_{$boundary->id}"));
            }
        }

        if (isset($params['country_id']) && $params['country_id']) {
            $query->ofDestinationCountryId($params['country_id']);
        }

//        if (isset($params['agreement_service_type']) && $params['agreement_service_type']) {
//            $query->ofAgreementServiceType($params['agreement_service_type']);
//        }

        // Exclude canceled packages
        $query->ofUnfinished();

        // Perform Joins
        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query = $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

//        return $query->groupBy('countries.id', 'countries.name', 'agreements.type')->orderBy('countries.name', 'asc');
        return $query->groupBy('countries.id', 'countries.name')->orderBy('countries.name', 'asc');
    }
}
