<?php

namespace App\Models;

use App\Presenters\ClientPresenter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Client
 *
 * @package App
 * @property Timezone $timezone
 * @property Country $country
 * @property Collection $agreements
 * @property Collection $marketplaces
 * @property Collection $trackers
 * @property Location $destinationLocation
 * @property Collection $legs
 * @property int $id
 * @property int $timezone_id
 * @property int|null $managed_by
 * @property string $code
 * @property string $name
 * @property string|null $acronym
 * @property int|null $country_id
 * @property string|null $access_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Client $manager
 * @mixin \Eloquent
 */

class Client extends Model implements HasPresenter
{
    protected $fillable = [
        'name',
        'acronym',
        'country_id',
        'timezone_id',
        'managed_by',
        'access_token',
        'code',
        'address1',
        'address2',
        'address3',
        'district',
        'city',
        'state',
        'postal_code',
        'phone',
        'paper',
        'access_token'
    ];

    protected $with = ['country', 'timezone'];

    protected $touches = ['agreements'];

    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function agreements()
    {
        return $this->hasMany(Agreement::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function manager()
    {
        return $this->hasOne(Client::class, 'id', 'managed_by');
    }

    public function marketplaces()
    {
        return $this->belongsToMany(Marketplace::class);
    }

    public function trackers()
    {
        return $this->belongsToMany(Tracker::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function scopeOfAccessToken($query, $access_token)
    {
        if (!$access_token) {
            return $query->whereNull('access_token');
        }

        return $query->whereAccessToken($access_token);
    }

    public function scopeOfMarketplaceId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('client_marketplace.marketplace_id', $id);
        } else {
            return !$id ? $query : $query->where('client_marketplace.marketplace_id', $id);
        }
    }

    public function scopeOfCountryId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('clients.country_id', $id);
        } else {
            return !$id ? $query : $query->where('clients.country_id', $id);
        }
    }

    public function scopeOfCode($query, $code)
    {
        if (is_array($code) && !empty($code)) {
            return $query->whereIn('clients.code', $code);
        } else {
            return !$code ? $query : $query->where('clients.code', $code);
        }
    }

    public function scopeOfName($query, $name)
    {
        if (is_array($name) && !empty($name)) {
            $query->where(function ($q2) use ($name) {
                collect($name)->each(function ($item) use($q2){
                    $q2->orWhere('clients.name', 'ilike', $item);
                });
            });
            return $query;
        } else {
            return !$name ? $query : $query->where('clients.name', 'ilike', $name);
        }
    }

    public function scopeOfServiceId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('services.id', $id);
        } else {
            return !$id ? $query : $query->where('services.id', $id);
        }
    }

    public function scopeOfExcludeClientId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereNotIn('clients.id', $id);
        } else {
            return !$id ? $query : $query->where('clients.id', '!=', $id);
        }
    }

    public function getTimezoneName()
    {
        return $this->timezone ? $this->timezone->name : null;
    }

    public function getTimezoneDescription()
    {
        return $this->timezone ? $this->timezone->description : null;
    }

    public function getCountryName()
    {
        return $this->country ? $this->country->name : null;
    }

    public function getCountryCode()
    {
        return $this->country ? $this->country->code : null;
    }

    public function getManagerName()
    {
        return $this->manager ? $this->manager->name : null;
    }

    public function getMarketplaceName(){
        $marketplace = $this->marketplaces->first();

        return $marketplace ? $marketplace->name : null;
    }

    public function isAliexpress()
    {
        return preg_match('/Aliexpress/i', $this->name);
    }

    public function getPresenterClass()
    {
        return ClientPresenter::class;
    }

}