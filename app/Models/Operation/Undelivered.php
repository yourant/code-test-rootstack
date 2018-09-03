<?php

namespace App\Models\Operation;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Metric
 * @package App\Models\Operation
 *
 * @property Batch $batch
 *
 * @property integer $total
 * @property integer $critical
 */
class Undelivered extends Model
{
    protected $table = 'operation_undelivered';

    protected $fillable = ['batch_id', 'total', 'critical'];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function undeliveredMetrics()
    {
        return $this->hasMany(UndeliveredMetric::class);
    }

    public function scopeOfBatchId($query, $batch_id)
    {
        return $query->where('operation_undelivered.batch_id', $batch_id);
    }
}