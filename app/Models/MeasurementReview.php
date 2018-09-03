<?php

namespace App\Models;

use App\Presenters\MeasurementReviewPresenter;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class MeasurementReview
 * @package App
 *
 * @property Package $package
 */
class MeasurementReview extends Model implements HasPresenter
{
    protected $fillable = ['package_id', 'modified_by', 'resolved_at'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function scopeOfPackageId($query, $id)
    {
        return $query->where('measurement_reviews.package_id', $id);
    }

    public function scopeOfResolved($query)
    {
        return $query->whereNotNull('measurement_reviews.resolved_at');
    }

    public function scopeOfUnresolved($query)
    {
        return $query->whereNull('measurement_reviews.resolved_at');
    }

    public function getPackageTrackingNumber()
    {
        return $this->package ? $this->package->tracking_number : null;
    }

    public function getPackageAgreementClientName()
    {
        return $this->package ? $this->package->getClientName() : null;
    }

    public function getPackageBillableWeight()
    {
        return $this->package ? $this->package->getBillableWeight() : null;
    }

    public function getPackageWidth()
    {
        return $this->package ? $this->package->width : null;
    }

    public function getPackageHeight()
    {
        return $this->package ? $this->package->height : null;
    }

    public function getPackageLength()
    {
        return $this->package ? $this->package->length : null;
    }

    public function getPackageWeight()
    {
        return $this->package ? $this->package->weight : null;
    }

    public function getPackageVolWeight()
    {
        return $this->package ? $this->package->vol_weight : null;
    }

    public function getPackageVerifiedWidth()
    {
        return $this->package ? $this->package->verified_width : null;
    }

    public function getPackageVerifiedHeight()
    {
        return $this->package ? $this->package->verified_height : null;
    }

    public function getPackageVerifiedLength()
    {
        return $this->package ? $this->package->verified_length : null;
    }

    public function getPackageVerifiedWeight()
    {
        return $this->package ? $this->package->verified_weight : null;
    }

    public function getPackageVerifiedVolWeight()
    {
        return $this->package ? $this->package->calculated_vol_weight : null;
    }

    public function getPackageLastVolumetricScaleMeasurementWidth()
    {
        return $this->package ? $this->package->getLastVolumetricScaleMeasurementWidth() : null;
    }

    public function getPackageLastVolumetricScaleMeasurementHeight()
    {
        return $this->package ? $this->package->getLastVolumetricScaleMeasurementHeight() : null;
    }

    public function getPackageLastVolumetricScaleMeasurementLength()
    {
        return $this->package ? $this->package->getLastVolumetricScaleMeasurementLength() : null;
    }

    public function getPackageLastVolumetricScaleMeasurementWeight()
    {
        return $this->package ? $this->package->getLastVolumetricScaleMeasurementWeight() : null;
    }

    public function getPackageLastVolumetricScaleMeasurementVolWeight()
    {
        return $this->package ? $this->package->getLastVolumetricScaleMeasurementVolWeight() : null;
    }

    public function getPackageLastVolumetricScaleMeasurementImageUrl()
    {
        return $this->package ? $this->package->getLastVolumetricScaleMeasurementImageUrl() : null;
    }

    public function getPresenterClass()
    {
        return MeasurementReviewPresenter::class;
    }
}