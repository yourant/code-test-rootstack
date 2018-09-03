<?php

namespace App\Models;

use App\Presenters\ClassificationPresenter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Classification
 *
 * @package App
 * @property Collection $checkpointCodes
 * @property int $id
 * @property string $key
 * @property string $name
 * @property string|null $type
 * @property string|null $leg
 * @property int $order
 * @mixin \Eloquent
 */

class Classification extends Model implements HasPresenter
{

    protected $fillable = ['key', 'name', 'type', 'order'];

    protected $visible = ['id', 'name'];

    public $timestamps = false;

//    protected $with = ['checkpointCodes'];

    public function checkpointCodes()
    {
        return $this->hasMany(CheckpointCode::class);
    }

    public function scopeOfKey($query, $key)
    {
        return !$key ? $query : $query->where('classifications.key', $key);
    }

    public function scopeOfName($query, $name)
    {
        return !$name ? $query : $query->where('classifications.name', 'ilike', $name);
    }

    public function scopeOfType($query, $name)
    {
        return !$name ? $query : $query->where('classifications.type', 'ilike', $name);
    }

    public function scopeOfLeg($query, $leg)
    {
        return !$leg ? $query : $query->where('classifications.leg', 'ilike', $leg);
    }

    public function isPostedAtOrigin()
    {
        return $this->key == 'posted_at_origin';
    }

    public function isInTransitToAirport()
    {
        return $this->key == 'in_transit_to_airport';
    }

    public function isArrivedAtAirport()
    {
        return $this->key == 'arrived_at_airport';
    }

    public function isDeliveredToTheCountry()
    {
        return $this->key == 'delivered_to_the_country';
    }

    public function isSentToCustoms()
    {
        return $this->key == 'sent_to_customs';
    }

    public function isOnDistributionToDeliveryCenter()
    {
        return $this->key == 'on_distribution_to_delivery_center';
    }

    public function getPresenterClass()
    {
        return ClassificationPresenter::class;
    }

//    public function isDelivered()
//    {
//        return $this->key == 'delivered' or preg_match('/delivered/i', $this->name);
//    }
}