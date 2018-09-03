<?php

namespace App\Models;

use App\Presenters\CheckpointCodePresenter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class CheckpointCode
 *
 * @package App
 * @property Classification $classification
 * @property Provider       $provider
 * @property Collection     $eventCodes
 * @property Collection     $checkpoint
 * @property int $id
 * @property int|null $provider_id
 * @property int|null $classification_id
 * @property string|null $key
 * @property string $type
 * @property int|null $code
 * @property string $description
 * @property string|null $category
 * @property string|null $description_en
 * @property int $delivered
 * @property int $returned
 * @property int $canceled
 * @property int $stalled
 * @property int $returning
 * @property int $clockstop
 * @property int $virtual
 * @property int $exceptional
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read mixed $classification_name
 * @property-read mixed $classification_type
 * @property-read mixed $provider_name
 * @mixin \Eloquent
 */
class CheckpointCode extends Model implements HasPresenter
{
    protected $fillable = [
        'provider_id',
        'classification_id',
        'key',
        'type',
        'code',
        'description',
        'category',
        'delivered',
        'returned',
        'canceled',
        'returning',
        'stalled',
        'clockstop',
        'virtual',
        'exceptional',
        'internal'
    ];

    protected $visible = ['id', 'key', 'type', 'code', 'description', 'description_alt', 'category'];

    public $with = ['classification'];

    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

//    public function milestones()
//    {
//        return $this->belongsToMany(Milestone::class);
//    }

    public function eventCodes()
    {
        return $this->belongsToMany(EventCode::class);
    }

    public function scopeOfId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('checkpoint_codes.id', $id);
        } else {
            return !$id ? $query : $query->where('checkpoint_codes.id', $id);
        }
    }

    public function scopeOfExcludeId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereNotIn('checkpoint_codes.id', $id);
        } else {
            return !$id ? $query : $query->where('checkpoint_codes.id', '!=', $id);
        }
    }

    public function scopeOfClassificationId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('checkpoint_codes.classification_id', $id);
        } else {
            return !$id ? $query : $query->where('checkpoint_codes.classification_id', $id);
        }
    }

    public function scopeOfProviderId($query, $provider_id)
    {
        return !$provider_id ? $query : $query->where('checkpoint_codes.provider_id', $provider_id);
    }

    public function scopeOfProviderName($query, $name)
    {
        return !$name ? $query : $query->where('providers.name', $name);
    }

    public function scopeOfType($query, $type)
    {
        return !$type ? $query : $query->where('checkpoint_codes.type', $type);
    }

    public function scopeOfCode($query, $code)
    {
        return !$code ? $query : $query->where('checkpoint_codes.code', $code);
    }

    public function scopeOfKey($query, $key)
    {
        return !$key ? $query : $query->where('checkpoint_codes.key', $key);
    }

    public function scopeOfCategory($query, $category)
    {
        return !$category ? $query : $query->where('checkpoint_codes.category', $category);
    }

    public function scopeOfDelivered($query) 
    { 
        return $query->where('checkpoint_codes.delivered', true); 
    }

    public function scopeOfReturned($query) { 
        return $query->where('checkpoint_codes.returned', true); 
    }

    public function scopeOfCanceled($query)
    {
        return $query->where('checkpoint_codes.canceled', true);
    }

    public function scopeOfStalled($query)
    {
        return $query->where('checkpoint_codes.stalled', true);
    }

    public function scopeOfReturning($query)
    {
        return $query->where('checkpoint_codes.returning', true);
    }

    public function scopeOfClockstop($query)
    {
        return $query->where('checkpoint_codes.clockstop', '>', 0);
    }

    public function scopeOfVirtual($query)
    {
        return $query->where('checkpoint_codes.virtual', true);
    }

    public function scopeOfDescription($query, $description)
    {
        return !$description ? $query : $query->where('checkpoint_codes.description', 'LIKE', "%{$description}%");
    }

    public function scopeOfClassificationType($query, $type)
    {
        if (is_array($type) && !empty($type)) {
            return $query->whereIn('classifications.type', $type);
        } else {
            return !$type ? $query : $query->where('classifications.type', $type);
        }
    }

    public function scopeOfClassificationName($query, $name)
    {
        if (is_array($name) && !empty($name)) {
            return $query->whereIn('classifications.name', $name);
        } else {
            return !$name ? $query : $query->where('classifications.name', $name);
        }
    }

    public function scopeOfClassificationLeg($query, $leg)
    {
        if (is_array($leg) && !empty($leg)) {
            return $query->whereIn('classifications.leg', $leg);
        } else {
            return !$leg ? $query : $query->where('classifications.leg', $leg);
        }
    }

    public function scopeOfWithoutClassification($query)
    {
        return $query->where('checkpoint_codes.classification_id', null);
    }

    public function scopeOfBagId($query, $bag_id)
    {
        if (is_array($bag_id) && !empty($bag_id)) {
            return $query->whereIn('bags.id', $bag_id);
        } else {
            return !$bag_id ? $query : $query->where('bags.id', $bag_id);
        }
    }

    public function isInTransitToAirport()
    {
        return $this->classification ? $this->classification->isInTransitToAirport() : false;
    }

    public function isDeliveredToTheCountry()
    {
        return $this->classification ? $this->classification->isDeliveredToTheCountry() : false;
    }

    public function isSentToCustoms()
    {
        return $this->classification ? $this->classification->isSentToCustoms() : false;
    }

    public function isArrivedAtAirport()
    {
        return $this->classification ? $this->classification->isArrivedAtAirport() : false;
    }

    public function isOnDistributionToDeliveryCenter()
    {
        return $this->classification ? $this->classification->isOnDistributionToDeliveryCenter() : false;
    }

    public function isDelivered()
    {
        return ($this->delivered);
    }

    public function isReturned()
    {
        return ($this->returned);
    }

    public function isCanceled()
    {
        return ($this->canceled);
    }

    public function isReturning()
    {
        return ($this->returning);
    }

    public function isStalled()
    {
        return ($this->stalled);
    }

    public function isClockstop()
    {
        return ($this->clockstop);
    }

    public function isVirtual()
    {
        return ($this->virtual);
    }

    public function isClockstopOfType($type = 1)
    {
        return $this->clockstop == $type;
    }

    public function getProviderNameAttribute()
    {
        return $this->provider ? $this->provider->name : null;
    }

    public function getClassificationNameAttribute()
    {
        return $this->classification ? $this->classification->name : null;
    }

    public function getClassificationTypeAttribute()
    {
        return $this->classification ? $this->classification->type : null;
    }
    
    public function getEventCode()
    {
        return $this->eventCodes->count() != 0 ? $this->eventCodes->first() : null;
    }

    public function getPresenterClass()
    {
        return CheckpointCodePresenter::class;
    }
}