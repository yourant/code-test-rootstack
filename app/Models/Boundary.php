<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Boundary
 *
 * @package App
 * @property int $lower
 * @property int $upper
 * @property Segment $segment
 * @mixin \Eloquent
 */
class Boundary extends Model
{
    public $timestamps = false;

    protected $fillable = ['segment_id', 'lower', 'upper'];

    public function segment() {
        return $this->belongsTo(Segment::class);
    }
}
