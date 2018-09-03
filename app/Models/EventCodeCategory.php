<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class EventCode
 *
 * @package App
 * @property string $name
 * @mixin \Eloquent
 */
class EventCodeCategory extends Model
{
    protected $fillable = [
        'name'
    ];

    public function eventCodes()
    {
        return $this->belongsToMany(EventCode::class);
    }

    public function getCheckpointCodeDescription()
    {
        return $this->checkpointCodes ? $this->checkpointCodes : null;
    }

    public function scopeOfName($query, $name)
    {
        return $query->where('event_code_categories.name', $name);
    }
}
