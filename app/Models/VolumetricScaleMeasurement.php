<?php

namespace App\Models;

use App\Presenters\VolumetricScaleMeasurementPresenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class VolumetricScaleMeasurement
 *
 * @package App
 * @property float $weight
 * @property float $width
 * @property float $height
 * @property float $length
 * @property float $vol_weight
 * @property string $image_url
 * @property VolumetricScale $volumetricScale
 * @property Package $package
 * @mixin \Eloquent
 */
class VolumetricScaleMeasurement extends Model implements HasPresenter
{
    use SoftDeletes;

    protected $fillable = [
        'package_id',
        'volumetric_scale_id',
        'weight',
        'width',
        'height',
        'length',
        'vol_weight',
        'image_url'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function volumetricScale()
    {
        return $this->belongsTo(VolumetricScale::class);
    }

    public function scopeOfVolumetricScaleId($query, $id)
    {
        return !$id ? $query : $query->where('volumetric_scale_measurements.volumetric_scale_id', $id);
    }

    public function getPackageTrackingNumber()
    {
        return $this->package ? $this->package->tracking_number : null;
    }

    public function getPackageVerifiedWeight()
    {
        return $this->package ? $this->package->verified_weight : null;
    }

    public function getPackageAgreementClientName()
    {
        return $this->package ? $this->package->getClientName() : null;
    }

    public function getPackageShipper()
    {
        return $this->package ? $this->package->shipper : null;
    }

    public function getVolumetricScaleCode()
    {
        return $this->volumetricScale ? $this->volumetricScale->code : null;
    }

    public function getVolumetricScaleName()
    {
        return $this->volumetricScale ? $this->volumetricScale->name : null;
    }

    public function getPresenterClass()
    {
        return VolumetricScaleMeasurementPresenter::class;
    }
}
