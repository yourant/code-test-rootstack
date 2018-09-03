<?php

namespace App\Models;

use App\Presenters\TimezonePresenter;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * App\Models\Timezone
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @mixin \Eloquent
 */
class Timezone extends Model implements HasPresenter {

    public $timestamps = false;

	protected $fillable = ['name', 'description'];

    protected $hidden = ['id'];

    public function getPresenterClass()
    {
        return TimezonePresenter::class;
    }

}