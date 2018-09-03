<?php

namespace App\Models;

use App\Presenters\CheckpointPresenter;
use Carbon\Carbon;
use Doctrine\DBAL\Events;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Checkpoint
 *
 * @package App
 * @property CheckpointCode $checkpointCode
 * @property Package $package
 * @property Timezone $timezone
 * @property int $id
 * @property int $package_id
 * @property int|null $checkpoint_code_id
 * @property int|null $timezone_id
 * @property int $manual
 * @property string|null $received_by
 * @property string|null $office
 * @property string|null $office_zip
 * @property string|null $city
 * @property string|null $details
 * @property \Carbon\Carbon $checkpoint_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin \Eloquent
 */
class Checkpoint extends Model implements HasPresenter
{

    protected $fillable = ['checkpoint_at', 'checkpoint_code_id', 'timezone_id', 'received_by', 'office', 'office_zip', 'manual', 'city', 'details'];

    protected $hidden = ['id', 'package_id', 'checkpoint_code_id', 'timezone_id', 'manual', 'created_at', 'updated_at'];

    public $with = ['checkpointCode', 'timezone'];

    public $dates = ['checkpoint_at'];

//    protected $touches = ['package'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function checkpointCode()
    {
        return $this->belongsTo(CheckpointCode::class);
    }

    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    public function scopeOfCreatedAtOlderThan($query, $date)
    {
        preg_match('/[0-24]{2}:[0-59]{2}:[0-59]{2}/', $date) ? $date : $date . '23:59:59';
        return !$date ? $query : $query->where('checkpoints.created_at', '<=', $date);
    }

    public function scopeOfCreatedAtNewerThan($query, $date)
    {
        preg_match('/[0-24]{2}:[0-59]{2}:[0-59]{2}/', $date) ? $date : $date . '00:00:00';
        return !$date ? $query : $query->where('checkpoints.created_at', '>=', $date);
    }

    public function getCheckpointAtIso8601Attribute()
    {
        return $this->checkpoint_at_carbon->toIso8601String();
    }

    public function getCheckpointAtCarbonAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->checkpoint_at, $this->getTimezoneName());
    }

    public function getPackageTrackingNumber()
    {
        return $this->package ? $this->package->tracking_number : null;
    }

    public function getTimezoneName()
    {
        return $this->timezone ? $this->timezone->name : null;
    }

    public function getTimezoneDescription()
    {
        return $this->timezone ? $this->timezone->description : null;
    }

    public function getCheckpointCodeId()
    {
        return $this->checkpointCode ? $this->checkpointCode->id: null;
    }

    public function getCheckpointCodeDescription()
    {
        return $this->checkpointCode ? $this->checkpointCode->description : null;
    }

    public function getCheckpointCodeCategory()
    {
        return $this->checkpointCode ? $this->checkpointCode->category : null;
    }

    public function getCheckpointCodeClassificationName()
    {
        return $this->checkpointCode ? $this->checkpointCode->classification_name : null;
    }

    public function getCheckpointCodeClassificationType()
    {
        return $this->checkpointCode ? $this->checkpointCode->classification_type : null;
    }

    public function getCheckpointCodeKey()
    {
        return $this->checkpointCode ? $this->checkpointCode->key : null;
    }

    public function getCheckpointCodeCode()
    {
        return $this->checkpointCode ? $this->checkpointCode->code : null;
    }

    public function getCheckpointCodeType()
    {
        return $this->checkpointCode ? $this->checkpointCode->type : null;
    }

    public function getCheckpointCodeProvider()
    {
        return $this->checkpointCode ? $this->checkpointCode->provider : null;
    }

    public function getCheckpointCodeEventCode()
    {
        return $this->checkpointCode ? $this->checkpointCode->getEventCode() : null;
    }

    public function isInTransitToAirport()
    {
        return $this->checkpointCode ? $this->checkpointCode->isInTransitToAirport() : false;
    }

    public function isDeliveredToTheCountry()
    {
        return $this->checkpointCode ? $this->checkpointCode->isDeliveredToTheCountry() : false;
    }

    public function isArrivedAtAirport()
    {
        return $this->checkpointCode ? $this->checkpointCode->isArrivedAtAirport() : false;
    }

    public function isOnDistributionToDeliveryCenter()
    {
        return $this->checkpointCode ? $this->checkpointCode->isOnDistributionToDeliveryCenter() : false;
    }

    public function isDelivered()
    {
        return $this->checkpointCode ? $this->checkpointCode->isDelivered() : false;
    }

    public function isReturned()
    {
        return $this->checkpointCode ? $this->checkpointCode->isReturned() : false;
    }

    public function isCanceled()
    {
        return $this->checkpointCode ? $this->checkpointCode->isCanceled() : false;
    }

    public function isReturning()
    {
        return $this->checkpointCode ? $this->checkpointCode->isReturning() : false;
    }

    public function isStalled()
    {
        return $this->checkpointCode ? $this->checkpointCode->isStalled() : false;
    }

    public function isClockstop()
    {
        return $this->checkpointCode ? $this->checkpointCode->isClockstop() : false;
    }

    public function isFinished()
    {
        return ($this->isDelivered() or $this->isReturned() or $this->isCanceled());
    }

    public function getCheckpointCodeClockstop()
    {
        return $this->checkpointCode ? $this->checkpointCode->clockstop : 0;
    }

    public function getPresenterClass()
    {
        return CheckpointPresenter::class;
    }
}