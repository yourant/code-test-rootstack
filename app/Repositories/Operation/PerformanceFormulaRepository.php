<?php

namespace App\Repositories\Operation;

use App\Repositories\AbstractRepository;
use App\Models\Operation\PerformanceFormula;

class PerformanceFormulaRepository extends AbstractRepository
{
    function __construct(PerformanceFormula $model)
    {
        $this->model = $model;
    }
}