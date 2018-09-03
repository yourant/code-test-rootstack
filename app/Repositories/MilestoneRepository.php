<?php

namespace App\Repositories;

use App\Models\CheckpointCode;
use App\Models\Milestone;

class MilestoneRepository extends AbstractRepository
{
    function __construct(Milestone $model)
    {
        $this->model = $model;
    }

    public function addCheckpointCode(Milestone $milestone, CheckpointCode $checkpointCode)
    {
        return $milestone->checkpointCodes()->attach($checkpointCode->id);
    }
}