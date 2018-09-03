<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\Models\AlertDetail
 *
 * @property-read \App\Models\Alert $alert
 * @property-read \App\Models\Classification $classification
 * @property-read mixed $classification_name
 * @property-read mixed $classification_type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AlertDetail ofAlertId($id)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AlertDetail ofClassificationId($id)
 * @mixin \Eloquent
 */
class AlertDetail extends Model
{

    protected $fillable = ['classification_id', 'days'];

    public $timestamps = false;

    public $with = ['classification'];

    public function alert()
    {
        return $this->belongsTo(Alert::class);
    }

    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }

    public function scopeOfAlertId($query, $id)
    {
        return !$id ? $query : $query->where('alert_details.alert_id', $id);
    }

    public function scopeOfClassificationId($query, $id)
    {
        return !$id ? $query : $query->where('alert_details.classification_id', $id);
    }

    public function getClassificationNameAttribute()
    {
        return $this->classification ? $this->classification->name : null;
    }

    public function getClassificationTypeAttribute()
    {
        return $this->classification ? $this->classification->type : null;
    }

    public function getClassificationCheckpointCodes()
    {
        if (!$this->classification) {
            return null;
        }

        $ccs = Collection::make();
        foreach ($this->classification->checkpointCodes as $cc) {
            $ccs->push($cc);
        }

        return $ccs;
    }
}