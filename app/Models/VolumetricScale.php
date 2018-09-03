<?php

namespace App\Models;

use App\Presenters\VolumetricScalePresenter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class VolumetricScale
 *
 * @package App
 * @property string $code
 * @property string $name
 * @property VolumetricScale $volumetricScale
 * @property Package $package
 * @property Collection $volumetricScaleMeasurements
 * @mixin \Eloquent
 */
class VolumetricScale extends Model implements HasPresenter
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name'
    ];

    public static function scopeOfCode($query, $code)
    {
        return $query->where('volumetric_scales.code', '=', $code);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function volumetricScaleMeasurements()
    {
        return $this->hasMany(VolumetricScaleMeasurement::class);
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }

    public function getPresenterClass()
    {
        return VolumetricScalePresenter::class;
    }
}
