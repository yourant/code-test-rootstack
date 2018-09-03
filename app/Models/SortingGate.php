<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class SortingGate
 * @package App
 *
 * @property Sorting $sorting
 * @property Collection $sortingGateCriterias
 */
class SortingGate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sorting_id',
        'number',
        'default',
        'name',
        'code',
        'deleted_at',
        'modified_by',
    ];

    public function sorting()
    {
        return $this->belongsTo(Sorting::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sortingGateCriterias()
    {
        return $this->hasMany(SortingGateCriteria::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function modifier()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }

    public function scopeOfSortingId($query, $sorting_id)
    {
        if (is_array($sorting_id) && !empty($sorting_id)) {
            return $query->whereIn('sorting_gates.sorting_id', $sorting_id);
        }

        return !$sorting_id ? $query : $query->where('sorting_gates.sorting_id', $sorting_id);
    }

    public function scopeOfNumber($query, $number)
    {
        if (is_array($number) && !empty($number)) {
            return $query->whereIn('sorting_gates.number', $number);
        }

        return !$number ? $query : $query->where('sorting_gates.number', $number);
    }

    public function getFirstSortingGateCriteria()
    {
        return $this->sortingGateCriterias ? $this->sortingGateCriterias->first() : null;
    }

    public function getSortingName()
    {
        return $this->sorting ? $this->sorting->name : null;
    }

    public function getSortingServiceCode()
    {
        return $this->sorting ? $this->sorting->getServiceCode() : null;
    }

    public function getSortingServiceCountryCode()
    {
        return $this->sorting ? $this->sorting->getServiceCountryCode() : null;
    }

    public function getSortingGateCriteriaZipCodeCountryCode()
    {
        $country_code = null;
        /** @var SortingGateCriteria $sortingGateCriteria */
        if ($sortingGateCriteria = $this->getFirstSortingGateCriteria()) {
            /** @var ZipCode $zipCode */
            if ($zipCode = $sortingGateCriteria->getFirstZipCode()) {
                $country_code = $zipCode->getAdminLevel3AdminLevel2AdminLevel1CountryCode();
            }
        }

        return $country_code;
    }
}
