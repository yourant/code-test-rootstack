<?php

namespace App\Models\Operation;

use App\Models\AdminLevel1;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Operation\StatePerformance
 *
 * @property int $id
 * @property int $panel_id
 * @property int $performance_formula_id
 * @property int $state_id
 * @property float $minimum
 * @property float $maximum
 * @property float|null $average
 * @property float|null $mean
 * @property float|null $percentile60
 * @property float|null $percentile75
 * @property float|null $percentile90
 * @property float|null $std_dev
 * @property int $package_count
 * @property string|null $frequencies
 * @property string $period_from
 * @property string $period_to
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 */
class StatePerformance extends Model
{
    use SoftDeletes;

    protected $table = 'operation_state_performances';

    protected $fillable = [
        'minimum',
        'maximum',
        'average',
        'mean',
        'percentile60',
        'percentile75',
        'percentile90',
        'std_dev',
        'package_count',
        'frequencies',
        'period_from',
        'period_to'
    ];

    public function panel()
    {
        return $this->belongsTo(Panel::class);
    }

    public function performanceFormula()
    {
        return $this->belongsTo(PerformanceFormula::class);
    }

    public function state()
    {
        return $this->belongsTo(AdminLevel1::class);
    }


}