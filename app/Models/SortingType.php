<?php

namespace App\Models;

use App\Presenters\SortingTypePresenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class SortingType
 * @package App
 */
class SortingType extends Model implements HasPresenter
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = ['name', 'description', 'modified_by'];

    /**
     * Get the presenter class.
     *
     * @return string
     */

    public function sortings()
    {
        return $this->belongsToMany(Sorting::class)->withTimestamps()->withPivot(['modified_by']);
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

    public function scopeOfId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('sorting_types.id', $id);
        } else {
            return !$id ? $query : $query->where('sorting_types.id', $id);
        }
    }

    public function scopeOfNameLike($query, $name)
    {
        return !$name ? $query : $query->where('sorting_types.name', 'ilike', "%{$name}%");
    }

    /**
     * @return int
     */
    public function isGeographical()
    {
        return preg_match('/geographical/i', $this->name);
    }

    /**
     * @param $sorting_type_name
     * @return null|string
     */
    public function getSortinTypeNameAttribute($sorting_type_name = null)
    {
        $sorting_type_name = $sorting_type_name ? $sorting_type_name : $this->name;
        if (preg_match('/^by.+value/i', $sorting_type_name)) {
            return 'By Value';
        } elseif (preg_match('/^geographical.+region/i', $sorting_type_name)) {
            return 'By Region';
        } elseif (preg_match('/^geographical.+town/i', $sorting_type_name)) {
            return 'By Town';
        } elseif (preg_match('/^geographical.+state/i', $sorting_type_name)) {
            return 'By State';
        } elseif (preg_match('/^particular.+weight/i', $sorting_type_name)) {
            return 'By Weight';
        } elseif (preg_match('/^particular.+criteria/i', $sorting_type_name)) {
            return 'By Criteria';
        } elseif (preg_match('/^geographical.+postal.+office/i', $sorting_type_name)) {
            return 'By Postal Office';
        }

        return null;
    }

    /**
     * @param $sorting_type_name
     * @return null|string
     */
    public function isByValue($sorting_type_name = null)
    {
        $sorting_type_name = $sorting_type_name ? $sorting_type_name : $this->name;

        return preg_match('/^by.+value/i', $sorting_type_name) ? true : false;
    }

    /**
     * @param
     * @return null|string
     */

    public function isByRegion($sorting_type_name = null)
    {
        $sorting_type_name = $sorting_type_name ? $sorting_type_name : $this->name;

        return preg_match('/^geographical.+region/i', $sorting_type_name) ? true : false;
    }

    /**
     * @param $sorting_type_name
     * @return null|string
     */

    public function isByTown($sorting_type_name = null)
    {
        $sorting_type_name = $sorting_type_name ? $sorting_type_name : $this->name;

        return preg_match('/^geographical.+town/i', $sorting_type_name) ? true : false;
    }

    /**
     * @param $sorting_type_name
     * @return null|string
     */

    public function isByState($sorting_type_name = null)
    {
        $sorting_type_name = $sorting_type_name ? $sorting_type_name : $this->name;

        return preg_match('/^geographical.+state/i', $sorting_type_name) ? true : false;
    }

    /**
     * @param $sorting_type_name
     * @return null|string
     */

    public function isByWeight($sorting_type_name = null)
    {
        $sorting_type_name = $sorting_type_name ? $sorting_type_name : $this->name;

        return preg_match('/^particular.+weight/i', $sorting_type_name) ? true : false;
    }

    /**
     * @param $sorting_type_name
     * @return null|string
     */

    public function isByPostalOffice($sorting_type_name = null)
    {
        $sorting_type_name = $sorting_type_name ? $sorting_type_name : $this->name;

        return preg_match('/^geographical.+postal.+office/i', $sorting_type_name) ? true : false;
    }

    /**
     * @param $sorting_type_name
     * @return null|string
     */

    public function isByCriteria($sorting_type_name = null)
    {
        $sorting_type_name = $sorting_type_name ? $sorting_type_name : $this->name;

        return preg_match('/^particular.+criteria/i', $sorting_type_name) ? true : false;
    }

    /**
     * Get the presenter class.
     *
     * @return string
     */
    public function getPresenterClass()
    {
        return SortingTypePresenter::class;
    }
}
