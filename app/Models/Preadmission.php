<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class Preadmission
 *
 * @package App
 * @property Collection $package
 * @property int $id
 * @property int $dispatch_id
 * @property string $reference
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class Preadmission extends Model
{
    protected $fillable = ['dispatch_id', 'reference'];

    protected $hidden = ['id'];

    public function packages()
    {
        return $this->belongsToMany(Package::class);
    }

}
