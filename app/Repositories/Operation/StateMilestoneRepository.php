<?php

namespace App\Repositories\Operation;

use App\Repositories\AbstractRepository;
use App\Models\Operation\StateMilestone;

class StateMilestoneRepository extends AbstractRepository
{
    function __construct(StateMilestone $model)
    {
        $this->model = $model;
    }
}
