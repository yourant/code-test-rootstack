<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Marketplace
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $access_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Client[] $clients
 * @property-read mixed $client_count
 * @mixin \Eloquent
 */
class Marketplace extends Model
{
    protected $fillable = ['name', 'code', 'access_token'];

    public function clients()
    {
        return $this->belongsToMany(Client::class);
    }

    public function scopeOfName($query, $name)
    {
        return !$name ? $query : $query->where('marketplaces.name', 'ilike', $name);
    }

    public function scopeOfCode($query, $code)
    {
        return !$code ? $query : $query->where('marketplaces.code', $code);
    }

    public static function scopeOfAccessToken($query, $access_token)
    {
        if (!$access_token) {
            return $query->whereNull('marketplaces.access_token');
        }

        return $query->where('marketplaces.access_token', $access_token);
    }

    public function containsClient(Client $client)
    {
        return $this->clients ? ($this->clients->find($client->id)) : false;
    }

    public function getClientCountAttribute()
    {
        return $this->clients ? $this->clients->count() : 0;
    }

    public function isLinio()
    {
        return preg_match('/MP3292/', $this->code);
    }
}
