<?php

namespace App\Models;

use App\Presenters\SortingPresenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Sorting
 * @package App
 *
 * @property Collection $sortingTypes
 * @property Service $service
 */
class Sorting extends Model implements HasPresenter
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'modified_by',
        'deleted_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sortingTypes()
    {
        return $this->belongsToMany(SortingType::class)
            ->withTimestamps()
            ->withPivot(['modified_by', 'deleted_at']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function service()
    {
        return $this->hasOne(Service::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sortingGates()
    {
        return $this->hasMany(SortingGate::class);
    }

    /**
     * @param $query
     * @param $service_id
     * @return mixed
     */
    public function scopeOfServiceId($query, $service_id)
    {
        if (is_array($service_id) && !empty($service_id)) {
            return $query->whereIn('services.id', $service_id);
        }

        return !$service_id ? $query : $query->where('services.id', $service_id);
    }

    /**
     * @param $query
     * @param $sorting_type_id
     * @return mixed
     */
    public function scopeOfSortingTypeId($query, $sorting_type_id)
    {
        if (is_array($sorting_type_id) && !empty($sorting_type_id)) {
            return $query->whereIn('sorting_sorting_type.sorting_type_id', $sorting_type_id);
        }

        return !$sorting_type_id ? $query : $query->where('sorting_sorting_type.sorting_type_id', $sorting_type_id);
    }

    public function scopeOfServiceNameOrKey($query, $service)
    {
        $query->join('services', 'services.sorting_id', '=', 'sortings.id');
        $query->where('services.name', 'like', "%{$service}%");
        $query->orWhere('services.code', 'like', "%" . strtoupper($service) . "%");

        return $query;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function modifier()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }

    /**
     * @return int
     */
    public function getGateCountAttribute()
    {
        return $this->sortingGates ? $this->sortingGates->count() : 0;
    }

    /**
     * @return SortingGate
     */
    public function getSortingGateDefault()
    {
        $sortingGate = $this->sortingGates->filter(function ($item) {
            return $item['default'] == true;
        });

        return $sortingGate->first();
    }

    /**
     * @return string
     */
    public function getServiceCode()
    {
        return $this->service ? $this->service->code : null;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->service ? $this->service->name : null;
    }

    /**
     * @return string
     */
    public function getServiceCountryCode()
    {
        return $this->service ? $this->service->getDestinationLocationCountryCode() : null;
    }

    /**
     * @return string
     */
    public function getServiceCountryName()
    {
        return $this->service ? $this->service->getDestinationLocationCountryName() : null;
    }

    /**
     * @return string
     */
    public function getServiceCountryContinentAbbreviation()
    {
        return $this->service ? $this->service->getDestinationLocationCountryContinentAbbreviation() : null;
    }

    public function containsSortingType(SortingType $sortingType)
    {
        return $this->sortingTypes ? ($this->sortingTypes->contains('id', $sortingType->id)) : false;
    }

    /**
     * @return string
     */
    public function getPresenterClass()
    {
        return SortingPresenter::class;
    }
}
