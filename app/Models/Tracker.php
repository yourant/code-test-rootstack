<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Tracker
 *
 * @package App
 * @property Collection $clients
 * @property int $id
 * @property string $name
 * @property string $access_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string|null $deleted_at
 * @property-read mixed $client_count
 * @mixin \Eloquent
 */
class Tracker extends Model
{
    protected $fillable = ['name', 'access_token'];

    public function clients()
    {
        return $this->belongsToMany(Client::class);
    }

    public static function scopeOfAccessToken($query, $access_token)
    {
        if (!$access_token) {
            return $query->whereNull('trackers.access_token');
        }

        return $query->where('trackers.access_token', $access_token);
    }

    public function containsClient(Client $client)
    {
        return $this->clients ? ($this->clients->find($client->id)) : false;
    }

    public function getClientCountAttribute()
    {
        return $this->clients ? $this->clients->count() : 0;
    }

    public function isYiqi()
    {
        return preg_match('/yiqi/i', $this->name);
    }

}
