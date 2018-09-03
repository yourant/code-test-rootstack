<?php

namespace App\Models\Operation;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Metric
 * @package App\Models\Operation
 *
 * @property Undelivered $undelivered
 *
 * @property integer $total
 * @property integer $critical
 */
class UndeliveredMetric extends Model
{
    protected $table = 'operation_undelivered_metrics';

    protected $fillable = ['undelivered_id', 'segment_id', 'total', 'segment', 'critical'];

    public function undelivered()
    {
        return $this->belongsTo(Undelivered::class);
    }

    public function segment()
    {
        return $this->belongsTo(Segment::class);
    }

    public function scopeOfSegmentId($query, $segment_id)
    {
        return $query->where('operation_undelivered_metrics.segment_id', $segment_id);
    }

    public function scopeOfCreatedBeforeThan($query, $date)
    {
        return !$date ? $query : $query->where('operation_undelivered_metrics.created_at', '<=', $date);
    }

    public function scopeOfCreatedAfterThan($query, $date)
    {
        return !$date ? $query : $query->where('operation_undelivered_metrics.created_at', '>=', $date);
    }

}