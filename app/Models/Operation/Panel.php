<?php

namespace App\Models\Operation;


use App\Models\Country;
use App\Presenters\Operation\PanelPresenter;
use App\Models\User;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Panel
 *
 * @package App\Models\Operation
 * @property Country $country
 * @property Collection $users
 * @property Collection $segments
 * @property string $name
 * @property string $service_type
 * @property int $id
 * @property int $country_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 */
class Panel extends Model implements HasPresenter
{
    use SoftDeletes;

    protected $table = 'operation_panels';

    protected $fillable = ['country_id', 'service_type', 'name'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function segments()
    {
        return $this->hasMany(Segment::class)->orderBy('operation_segments.position');
    }

    public function stateMilestones()
    {
        return $this->hasMany(StateMilestone::class);
    }

    public function providers()
    {
        return $this->belongsToMany(Provider::class, 'operation_panel_provider');
    }

    public function getCountryNameAttribute()
    {
        return $this->country ? $this->country->name : null;
    }

    public function getCountryCodeAttribute()
    {
        return $this->country ? $this->country->code : null;
    }

    public function getPresenterClass()
    {
        return PanelPresenter::class;
    }
}