<?php

namespace App\Models;

use App\Presenters\PackageItemPresenter;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class PackageItem
 *
 * @package App
 * @property Package $package
 * @property int $id
 * @property int $package_id
 * @property string|null $part_no
 * @property string $description
 * @property int $quantity
 * @property float|null $value
 * @property float|null $net_weight
 * @property string|null $hs_code
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin \Eloquent
 */


class PackageItem extends Model implements HasPresenter
{

    protected $fillable = ['part_no', 'description', 'quantity', 'value', 'net_weight', 'hs_code'];

    protected $hidden = ['package_id', 'created_at', 'updated_at'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function getPresenterClass()
    {
        return PackageItemPresenter::class;
    }

}