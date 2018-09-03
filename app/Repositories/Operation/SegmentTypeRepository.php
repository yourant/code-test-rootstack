<?php

namespace App\Repositories\Operation;

use App\Repositories\AbstractRepository;
use App\Models\Operation\SegmentType;

class SegmentTypeRepository extends AbstractRepository
{
    function __construct(SegmentType $model)
    {
        $this->model = $model;
    }
}
