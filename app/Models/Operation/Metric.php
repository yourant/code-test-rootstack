<?php

namespace App\Models\Operation;

use App\Models\Package;
use App\Presenters\Operation\MetricPresenter;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Metric
 *
 * @package App\Models\Operation
 * @property Batch $batch
 * @property Milestone $milestone
 * @property Package $package
 * @property integer $stalled
 * @property integer $controlled
 * @property integer $total
 * @property int $id
 * @property int $batch_id
 * @property int $milestone_id
 * @property int $package_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Metric extends Model implements HasPresenter
{
    protected $table = 'operation_metrics';

    protected $fillable = ['package_id', 'milestone_id', 'batch_id', 'stalled', 'segment', 'controlled', 'total'];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function scopeOfBatchId($query, $batch_id)
    {
        return $query->where('operation_metrics.batch_id', $batch_id);
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

//    public function getPackageAgreementUnderControlDays()
//    {
//        return $this->package->getAgreementControlledTransitDays();
//    }

    public function getPackageDeliveryRouteUnderControlDays()
    {
        return $this->package->getDeliveryRouteControlledTransitDays();
    }

    public function getPackageBagDispatchCode()
    {
        return $this->package->getBagDispatchCode();
    }

    public function getPackageLastCheckpoint()
    {
        return $this->package->lastCheckpoint;
    }

    public function getPresenterClass()
    {
        return MetricPresenter::class;
    }

}