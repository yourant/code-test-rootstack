<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Segment
 *
 * @package App
 * @property Collection $boundaries
 * @property Collection $milestones
 * @mixin \Eloquent
 */
class Segment extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'description', 'position'];

    public function boundaries()
    {
        return $this->hasMany(Boundary::class)->orderBy('lower');
    }

    public function milestones()
    {
        return $this->belongsToMany(Milestone::class);
    }

    public function isDistribution()
    {
        return preg_match('/distribution/i', $this->description);
    }
}
