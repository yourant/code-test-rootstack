<?php

namespace App\Models;

use App\Models\Operation\StateMilestone;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Milestone
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CheckpointCode[] $checkpointCodes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Segment[] $segments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Operation\StateMilestone[] $stateMilestones
 * @mixin \Eloquent
 */
class Milestone extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'description', 'position'];

    public function segments()
    {
        return $this->belongsToMany(Segment::class);
    }

    public function checkpointCodes()
    {
        return $this->belongsToMany(CheckpointCode::class);
    }

    public function stateMilestones()
    {
        return $this->hasMany(StateMilestone::class);
    }
}
