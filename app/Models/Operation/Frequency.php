<?php

namespace App\Models\Operation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Operation\Frequency
 *
 * @property int $id
 * @property string $key
 * @property string $value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Frequency extends Model
{
    use SoftDeletes;

    protected $table = 'operation_frequencies';

    protected $fillable = ['key', 'value'];
}