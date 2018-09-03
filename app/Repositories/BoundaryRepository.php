<?php

namespace App\Repositories;

use App\Models\Boundary;

class BoundaryRepository extends AbstractRepository
{
    function __construct(Boundary $model)
    {
        $this->model = $model;
    }
}