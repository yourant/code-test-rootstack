<?php

namespace App\Models;

use App\Presenters\EventCodePresenter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class EventCode
 *
 * @package App
 * @property int $id
 * @property Collection $checkpointCodes
 * @mixin \Eloquent
 */
class EventCode extends Model implements HasPresenter
{
    protected $fillable = [
        'key',
        'description',
        'position',
        'event_code_category_id'
    ];

    public function eventCodeCategory()
    {
        return $this->belongsTo(EventCodeCategory::class);
    }

    public function checkpointCodes()
    {
        return $this->belongsToMany(CheckpointCode::class);
    }

    public function getCheckpointCodeDescription()
    {
        return $this->checkpointCodes ? $this->checkpointCodes : null;
    }

    public function scopeOfKey($query, $key)
    {
        if (is_array($key) && !empty($key)) {
            return $query->whereIn('event_codes.key', $key);
        } else {
            return !$key ? $query : $query->where('event_codes.key', $key);
        }
    }

    public function scopeOfDescription($query, $description)
    {
        if (is_array($description) && !empty($description)) {
            return $query->whereIn('event_codes.description', $description);
        } else {
            return !$description ? $query : $query->where('event_codes.description', $description);
        }
    }

    public function scopeOfClassificationTypeCheckpoint($query, $classification_type_checkpoint)
    {
        if (is_array($classification_type_checkpoint) && !empty($classification_type_checkpoint)) {
            return $query->whereIn('classifications.type', $classification_type_checkpoint);
        } else {
            return !$classification_type_checkpoint ? $query : $query->where('classifications.type', $classification_type_checkpoint);
        }
    }

    public function scopeOfClassificationNameCheckpoint($query, $classification_name_checkpoint)
    {
        if (is_array($classification_name_checkpoint) && !empty($classification_name_checkpoint)) {
            return $query->whereIn('classifications.name', $classification_name_checkpoint);
        } else {
            return !$classification_name_checkpoint ? $query : $query->where('classifications.name', $classification_name_checkpoint);
        }
    }

    public function getEventCodeCategoryName()
    {
        return $this->eventCodeCategory ? $this->eventCodeCategory->name : null;
    }

    public function getPresenterClass()
    {
        return EventCodePresenter::class;
    }
}
