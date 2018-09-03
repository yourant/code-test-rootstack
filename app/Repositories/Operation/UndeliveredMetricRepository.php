<?php

namespace App\Repositories\Operation;

use App\Models\Operation\Segment;
use App\Models\Operation\UndeliveredMetric;
use App\Repositories\AbstractRepository;
use Carbon\Carbon;

class UndeliveredMetricRepository extends AbstractRepository
{
    function __construct(UndeliveredMetric $model)
    {
        $this->model = $model;
    }


    public function calculate(Segment $segment, Carbon $date)
    {
        $query = $this->model
            ->select('operation_undelivered_metrics.*')
            ->addSelect('operation_batches.created_at as batch_date')
            ->join('operation_undelivered', 'operation_undelivered_metrics.undelivered_id', '=', 'operation_undelivered.id')
            ->join('operation_batches', 'operation_undelivered.batch_id', '=', 'operation_batches.id')
            ->ofSegmentId($segment->id)
            ->where('operation_batches.created_at', '>=', $date->toDateTimeString())
            ->orderBy('operation_batches.id', 'asc');

        return $query->get();
    }
}
