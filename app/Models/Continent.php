<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Continent
 * @package App\Models
 *
 * @property string $name
 * @property string $abbreviation
 */
class Continent extends Model
{
    protected $fillable = ['name', 'abbreviation'];

    public $timestamps = false;

    public function countries()
    {
        return $this->hasMany(Country::class);
    }
}