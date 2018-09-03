<?php

namespace App\Models\Operation;

use App\Presenters\Operation\BatchPresenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * App\Models\Operation\Batch
 *
 * @property int $id
 * @property int $frequency_id
 * @property int|null $panel_id
 * @property string $value
 * @property int $processed
 * @property int $total
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 */
class Batch extends Model implements HasPresenter
{
    use SoftDeletes;

    protected $table = 'operation_batches';

    protected $fillable = ['panel_id', 'frequency_id', 'value', 'processed', 'total', 'archived'];

    public function frequency()
    {
        return $this->belongsTo(Frequency::class);
    }

    public function panel()
    {
        return $this->belongsTo(Panel::class);
    }

    public function metrics()
    {
        return $this->hasMany(Metric::class);
    }

    public function stateMilestoneMetrics()
    {
        return $this->hasMany(StateMilestoneMetric::class);
    }

    public function scopeOfPanelId($query, $id)
    {
        return $query->where('operation_batches.panel_id', $id);
    }

    public function scopeOfFrequencyId($query, $id)
    {
        return $query->where('operation_batches.frequency_id', $id);
    }

    public function scopeOfReady($query)
    {
        return $query
            ->whereColumn('operation_batches.processed', '>=', 'operation_batches.total')
            ->where('operation_batches.total', '<>', 0);
    }

    public function scopeOfArchived($query)
    {
        return $query->where('operation_batches.archived', true);
    }

    public function scopeOfUnarchived($query)
    {
        return $query->where('operation_batches.archived', false);
    }

    public function scopeOfValue($query, $value)
    {
        return $query->where('operation_batches.value', $value);
    }

    public function getPanelName()
    {
        return $this->panel ? $this->panel->name : null;
    }

    public function isReady()
    {
        return ($this->processed >= $this->total);
    }

    public function getPresenterClass()
    {
        return BatchPresenter::class;
    }
}