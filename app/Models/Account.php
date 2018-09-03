<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Account
 *
 * @package App
 * @property Client $managed_by
 * @property Client $owned_by
 * @property int $id
 * @property-read \App\Models\Client $manager
 * @property-read \App\Models\Client $owner
 * @mixin \Eloquent
 */
class Account extends Model
{
    protected $fillable = ['managed_by', 'owned_by'];

    public function manager()
    {
        return $this->belongsTo(Client::class, 'managed_by');
    }

    public function owner()
    {
        return $this->belongsTo(Client::class, 'owned_by');
    }

    public function scopeOfManagedBy($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('accounts.managed_by', $id);
        } else {
            return !$id ? $query : $query->where('accounts.managed_by', $id);
        }
    }

    public function scopeOfOwnedBy($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('accounts.owned_by', $id);
        } else {
            return !$id ? $query : $query->where('accounts.owned_by', $id);
        }
    }

}