<?php

namespace App\Repositories\Operation;

use App\Models\Operation\Batch;
use App\Models\Operation\Segment;
use App\Models\Operation\Undelivered;
use App\Repositories\AbstractRepository;

class UndeliveredRepository extends AbstractRepository
{
    function __construct(Undelivered $model)
    {
        $this->model = $model;
    }
}
