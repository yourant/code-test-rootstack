<?php

namespace App\Models\Operation;

use App\Models\AdminLevel1;
use App\Presenters\Operation\StateMilestonePresenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class StateMilestone
 *
 * @package App\Models\Operation
 * @property Milestone $milestone
 * @property AdminLevel1 $state
 * @property int $id
 * @property int $milestone_id
 * @property int|null $admin_level_1_id
 * @property int $days
 * @property int|null $warning1
 * @property int|null $warning2
 * @property int|null $critical1
 * @property int|null $critical2
 * @property int|null $critical3
 * @property int|null $critical4
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 */
class StateMilestone extends Model implements HasPresenter
{
    use SoftDeletes;

    protected $table = 'operation_state_milestones';

    protected $fillable = ['admin_level_1_id', 'days', 'accumulated_days', 'warning1', 'warning2', 'critical1', 'critical2', 'critical3', 'critical4'];

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function state()
    {
        return $this->belongsTo(AdminLevel1::class, 'admin_level_1_id');
    }

    public function getStateNameAttribute()
    {
        return $this->state ? $this->state->name : null;
    }

    public function getStateCountryId()
    {
        return $this->state ? $this->state->country_id : null;
    }

    public function getStateRegionName()
    {
        return $this->state ? $this->state->getRegionName() : null;
    }

    public function getPresenterClass()
    {
        return StateMilestonePresenter::class;
    }
}
