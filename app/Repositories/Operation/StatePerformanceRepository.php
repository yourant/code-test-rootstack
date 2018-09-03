<?php

namespace App\Repositories\Operation;

use App\Repositories\AbstractRepository;
use App\Models\Operation\StatePerformance;

class StatePerformanceRepository extends AbstractRepository
{
    function __construct(StatePerformance $model)
    {
        $this->model = $model;
    }
}
