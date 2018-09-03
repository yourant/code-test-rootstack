<?php

namespace App\Repositories;

use App\Models\Milestone;
use App\Models\Segment;

class SegmentRepository extends AbstractRepository
{
    function __construct(Segment $model)
    {
        $this->model = $model;
    }

    public function addBoundary(Segment $segment, $lower, $upper = null)
    {
        return $segment->boundaries()->create(compact('lower', 'upper'));
    }

    public function addMilestone(Segment $segment, Milestone $milestone)
    {
        return $segment->milestones()->attach($milestone->id);
    }
}