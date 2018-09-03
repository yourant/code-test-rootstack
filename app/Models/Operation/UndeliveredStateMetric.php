<?php

namespace App\Models\Operation;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Metric
 * @package App\Models\Operation
 *
 * @property Undelivered $undelivered
 *
 * @property integer $total
 * @property integer $critical
 */
class UndeliveredStateMetric extends Model
{
    protected $table = 'operation_undelivered_state_metrics';

    protected $fillable = ['undelivered_id', 'state_milestone_id', 'total', 'critical'];

    public function undelivered()
    {
        return $this->belongsTo(Undelivered::class);
    }

    public function stateMilestone()
    {
        return $this->belongsTo(StateMilestone::class);
    }

    public function scopeOfStateMilestoneId($query, $id)
    {
        return $query->where('operation_undelivered_state_metrics.state_milestone_id', $id);
    }

}