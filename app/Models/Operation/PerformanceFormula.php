<?php

namespace App\Models\Operation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Operation\PerformanceFormula
 *
 * @property int $id
 * @property string $key
 * @property string $name
 * @property string|null $description
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 */
class PerformanceFormula extends Model
{
    use SoftDeletes;

    protected $table = 'operation_performance_formulas';

    protected $fillable = ['key', 'name', 'description'];
}