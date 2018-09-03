<?php

namespace App\Models;

use App\Presenters\PostalOfficeTypePresenter;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class PostalOfficeType
 *
 * @package App
 * @property PostalOffice $postalOffice
 * @property int $id
 * @property string $name
 * @mixin \Eloquent
 */
class PostalOfficeType extends Model implements HasPresenter
{

    public $timestamps = false;

    protected $fillable = ['name'];

    public function postalOffice()
    {
        return $this->belongsTo(PostalOffice::class);
    }

    public function getPresenterClass()
    {
        return PostalOfficeTypePresenter::class;
    }
}
