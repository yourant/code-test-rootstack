<?php

namespace App\Models\Operation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SegmentType
 *
 * @package App\Models\Operation
 * @property int $id
 * @property string $key
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 */
class SegmentType extends Model
{
    use SoftDeletes;

    protected $table = 'operation_segment_types';

    protected $fillable = ['key', 'name'];

    public function isLastMile()
    {
        return $this->key == 'last_mile';
    }

    public function isPickAndPack()
    {
        return $this->key == 'pick_pack';
    }
}