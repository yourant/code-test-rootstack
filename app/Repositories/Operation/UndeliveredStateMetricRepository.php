<?php

namespace App\Repositories\Operation;

use App\Models\Operation\UndeliveredStateMetric;
use App\Repositories\AbstractRepository;

class UndeliveredStateMetricRepository extends AbstractRepository
{
    function __construct(UndeliveredStateMetric $model)
    {
        $this->model = $model;
    }
}
