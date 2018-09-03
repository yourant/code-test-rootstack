<?php

namespace App\Models;

use App\Presenters\ZonePresenter;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Zone
 *
 * @property int $id
 * @property string $name
 * @mixin \Eloquent
 */
class Zone extends Model
{

    public $timestamps = false;

    protected $fillable = ['name'];

    protected $hidden = ['id'];

}