<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ServiceType
 *
 * @property string $key
 * @property string $description
 * @mixin \Eloquent
 */
class ServiceType extends Model
{
    protected $fillable = [
        'key',
        'description'
    ];

    public function isPriority()
    {
        return $this->key == 'priority';
    }
    
    public function isRegistered()
    {
        return $this->key == 'registered';
    }
    
    public function isStandard()
    {
        return $this->key == 'standard';
    }
}
