<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\Models\Alert
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AlertDetail[] $alertDetails
 * @property-read mixed $alert_details_count
 * @property-read mixed $provider_name
 * @property-read \App\Models\Provider $provider
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert ofName($name)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert ofProviderId($id)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert ofSubtype($subtype)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert ofType($type)
 * @mixin \Eloquent
 */
class Alert extends Model
{

    protected $fillable = ['provider_id', 'name', 'type', 'subtype', 'delivery_standard_days'];

    public $timestamps = false;

    protected $with = ['alertDetails'];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function alertDetails()
    {
        return $this->hasMany(AlertDetail::class);
    }

    public function getAlertDetailsClassificationsCheckpointCodes()
    {
        if (!$this->alertDetails) {
            return null;
        }

        $o = Collection::make();
        foreach ($this->alertDetails as $ad) {
            if ($ccs = $ad->getClassificationCheckpointCodes()) {
                foreach ($ccs as $cc) {
                    $o->push($cc);
                }
            }
        }

        return $o;
    }

    public function getAlertDetailsCountAttribute()
    {
        return $this->alertDetails->count();
    }

    public function scopeOfProviderId($query, $id)
    {
        return !$id ? $query : $query->where('alerts.provider_id', $id);
    }

    public function scopeOfName($query, $name)
    {
        return !$name ? $query : $query->where('alerts.name', 'ilike', $name);
    }

    public function scopeOfType($query, $type)
    {
        if (is_array($type) && !empty($type)) {
            $query->where(function ($q2) use ($type) {
                collect($type)->each(function ($item) use($q2){
                    $q2->orWhere('alerts.type', 'ilike', $item);
                });
            });
            return $query;
        } else {
            return !$type ? $query : $query->where('alerts.type', 'ilike', $type);
        }
    }

    public function scopeOfSubtype($query, $subtype)
    {
        return !$subtype ? $query : $query->where('alerts.subtype', 'ilike', $subtype);
    }

    public function isInteriorSubtype()
    {
        return $this->subtype == 'Interior';
    }

    public function isFederalDistrictSubtype()
    {
        return $this->subtype == 'Federal District';
    }

    public function isUnclassifiedSubtype()
    {
        return $this->subtype == 'Unclassified';
    }

    public function getProviderNameAttribute()
    {
        return $this->provider ? $this->provider->name : null;
    }
}