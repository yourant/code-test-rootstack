<?php

namespace App\Repositories\Operation;

use App\Repositories\AbstractRepository;
use App\Models\Operation\Performance;

class PerformanceRepository extends AbstractRepository
{
    function __construct(Performance $model)
    {
        $this->model = $model;
    }
}