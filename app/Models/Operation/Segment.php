<?php

namespace App\Models\Operation;

use App\Presenters\Operation\SegmentPresenter;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Segment
 *
 * @package App\Models\Operation
 * @property Panel $panel
 * @property SegmentType $segmentType
 * @property Segment $parent
 * @property Collection $milestones
 * @property int $id
 * @property int $panel_id
 * @property int $segment_type_id
 * @property int|null $parent_id
 * @property string|null $name
 * @property int $position
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 */
class Segment extends Model implements HasPresenter
{
    use SoftDeletes;

    protected $table = 'operation_segments';

    protected $fillable = ['panel_id', 'segment_type_id', 'parent_id', 'name', 'duration', 'position'];

    public function panel()
    {
        return $this->belongsTo(Panel::class);
    }

    public function segmentType()
    {
        return $this->belongsTo(SegmentType::class);
    }

    public function parent()
    {
        return $this->belongsTo(Segment::class);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    public function getSegmentTypeNameAttribute()
    {
        return $this->segmentType ? $this->segmentType->name : null;
    }

    public function isSegmentTypeOfLastMile()
    {
        return $this->segmentType ? $this->segmentType->isLastMile() : null;
    }

    public function isSegmentTypeOfPickAndPack()
    {
        return $this->segmentType ? $this->segmentType->isPickAndPack() : null;
    }

    public function getPresenterClass()
    {
        return SegmentPresenter::class;
    }
}