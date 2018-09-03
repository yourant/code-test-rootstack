<?php

namespace App\Models\Operation;

use App\Models\Package;
use App\Presenters\Operation\StateMilestoneMetricPresenter;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * App\Models\Operation\StateMilestoneMetric
 *
 * @property int $id
 * @property int|null $batch_id
 * @property int $state_milestone_id
 * @property int $package_id
 * @property int|null $stalled
 * @property int|null $controlled
 * @property int|null $total
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property Package $package
 */
class StateMilestoneMetric extends Model implements HasPresenter
{
    protected $table = 'operation_state_milestone_metrics';

    protected $fillable = [
        'batch_id',
        'state_milestone_id',
        'package_id',
        'stalled',
        'controlled',
        'total'
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function stateMilestone()
    {
        return $this->belongsTo(StateMilestone::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function scopeOfBatchId($query, $batch_id)
    {
        return $query->where('operation_state_milestone_metrics.batch_id', $batch_id);
    }

    public function scopeOfStateMilestoneId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('operation_state_milestone_metrics.state_milestone_id', $id);
        } else {
            return !$id ? $query : $query->where('operation_state_milestone_metrics.state_milestone_id', $id);
        }
    }

    public function getPackageTrackingNumber()
    {
        return $this->package->tracking_number;
    }

//    public function getPackageAgreementCountryName()
//    {
//        return $this->package->getAgreementCountryName();
//    }

    public function getPackageAgreementServiceDestinationLocationCountryName()
    {
        return $this->package->getAgreementServiceDestinationLocationCountryName();
    }

    public function getPackageClientName()
    {
        return $this->package->getClientName();
    }

    public function getPackageBagDispatchCode()
    {
        return $this->package->getBagDispatchCode();
    }

    public function getPackageLastCheckpoint()
    {
        return $this->package->lastCheckpoint;
    }

    public function getPackageZipCodeCode()
    {
        return $this->package ? $this->package->getZipCodeCode() : null;
    }

    public function getPackageZipCodeTownshipTownStateName()
    {
        return $this->package ? $this->package->getZipCodeAdminLevel3AdminLevel2AdminLevel1Name() : null;
    }

    public function getPackageZip()
    {
        return $this->package ? $this->package->zip : null;
    }

    public function getPackageState()
    {
        return $this->package ? $this->package->state : null;
    }

    public function getStateMilestoneStateName()
    {
        return $this->stateMilestone ? $this->stateMilestone->state_name : null;
    }

    public function getStateMilestoneStateRegionName()
    {
        return $this->stateMilestone ? $this->stateMilestone->getStateRegionName() : null;
    }

    public function getPresenterClass()
    {
        return StateMilestoneMetricPresenter::class;
    }
}