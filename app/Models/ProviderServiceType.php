<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProviderServiceType
 *
 * @package App
 * @property Collection $providerService
 * @property int $id
 * @property string $key
 * @property string $name
 * @mixin \Eloquent
 */
class ProviderServiceType extends Model
{
    protected $fillable = ['key', 'name'];

    public $timestamps = false;

    public function providerServices()
    {
        return $this->hasMany(ProviderService::class);
    }
    
    public function isDistribution()
    {
        return $this->key == 'distribution';
    }
}
