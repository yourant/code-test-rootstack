<?php

namespace App\Repositories\Operation;

use App\Models\CheckpointCode;
use App\Models\Operation\StateMilestone;
use App\Repositories\AbstractRepository;
use App\Models\Operation\Milestone;

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

    public function addStateMilestone(Milestone $milestone, StateMilestone $stateMilestone)
    {
        return $milestone->stateMilestones();
    }
}
