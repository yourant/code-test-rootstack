<?php

namespace App\Models\Operation;

use App\Models\CheckpointCode;
use App\Presenters\Operation\MilestonePresenter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Milestone
 *
 * @package App\Models\Operation
 * @property Segment $segment
 * @property Collection $checkpointCodes
 * @property Collection $stateMilestones
 * @property int $warning1
 * @property int $warning2
 * @property int $critical1
 * @property int $critical2
 * @property int $critical3
 * @property int $critical4
 * @property int $position
 * @property int $id
 * @property int $segment_id
 * @property string|null $name
 * @property int|null $days
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Milestone extends Model implements HasPresenter
{
    use SoftDeletes;

    protected $table = 'operation_milestones';

    protected $fillable = ['name', 'days', 'accumulated_days', 'warning1', 'warning2', 'critical1', 'critical2', 'critical3', 'critical4', 'position'];

    public function segment()
    {
        return $this->belongsTo(Segment::class);
    }

    public function stateMilestones()
    {
        return $this->hasMany(StateMilestone::class);
    }

    public function checkpointCodes()
    {
        return $this->belongsToMany(CheckpointCode::class, 'operation_milestone_checkpoint_code')->withTimestamps();
    }

    public function setSegment(Milestone $milestone, Segment $segment)
    {
        $milestone->segment()->associate($segment);

        return $milestone->save();
    }

    public function getSegmentPanel()
    {
        return $this->segment ? $this->segment->panel : null;
    }

    public function calculateStalledDaysForPackage(Package $package)
    {

    }
    
    public function hasStates()
    {
        return $this->stateMilestones()->count() > 0;
    }

    public function getPresenterClass()
    {
        return MilestonePresenter::class;
    }
}