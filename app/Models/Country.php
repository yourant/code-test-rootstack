<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Country
 *
 * @package App
 * @property Collection $adminLevels1
 * @property Collection $regions
 * @property Continent $continent
 * @property int $id
 * @property string $name
 * @property string $code
 * @mixin \Eloquent
 */
class Country extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'code'];

    public function adminLevels1()
    {
        return $this->hasMany(AdminLevel1::class);
    }

    public function regions()
    {
        return $this->hasMany(Region::class);
    }

    public function continent()
    {
        return $this->belongsTo(Continent::class);
    }

    public function scopeOfCode($query, $code)
    {
        return $query->where('countries.code', 'ilike', $code);
    }

    public function scopeOfName($query, $name)
    {
        return $query->where('countries.name', 'ilike', $name);
    }

    public function getContinentAbbreviation()
    {
        return $this->continent ? $this->continent->abbreviation : null;
    }

    public function isMexico()
    {
        return ($this->code == 'MX');
    }

    public function isChile()
    {
        return ($this->code == 'CL');
    }
}
