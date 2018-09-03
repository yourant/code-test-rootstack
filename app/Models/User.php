<?php

namespace App\Models;

use App\Presenters\UserPresenter;
use McCool\LaravelAutoPresenter\HasPresenter;
use Cartalyst\Sentinel\Users\EloquentUser;

/**
 * App\Models\User
 *
 * @property int $id
 * @property int|null $client_id
 * @property int|null $marketplace_id
 * @property string $email
 * @property string $first_name
 * @property string|null $last_name
 * @property array $permissions
 * @property string|null $alternative_email
 * @property string $password
 * @property string|null $last_login
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin \Eloquent
 */
class User extends EloquentUser implements HasPresenter
{
    protected $with = ['client'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'marketplace_id',
        'email',
        'password',
        'last_name',
        'first_name',
        'permissions',
        'printnode_account_id',
        'printnode_account_firstname',
        'printnode_account_lastname',
        'printnode_account_email',
        'printnode_account_password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }

    public function scopeOfEmailLike($query, $keywords)
    {
        return !$keywords ? $query : $query->where('users.email', 'like', "%{$keywords}%");
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getClientNameAttribute()
    {
        return $this->client ? $this->client->name : null;
    }

    public function getClientAccessTokenAttribute()
    {
        return $this->client ? $this->client->access_token : null;
    }

    public function getMarketplaceNameAttribute()
    {
        return $this->marketplace ? $this->marketplace->name : null;
    }

    public function getMarketplaceAccessTokenAttribute()
    {
        return $this->marketplace ? $this->marketplace->access_token : null;
    }

    public function getClientAgreements()
    {
        return $this->isClient() ? $this->client->agreements : null;
    }

    public function isClient()
    {
        return $this->client ? true : false;
    }

    public function isMarketplace()
    {
        return $this->marketplace ? true : false;
    }

    public function getPresenterClass()
    {
        return UserPresenter::class;
    }
}
