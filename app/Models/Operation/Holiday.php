<?php

namespace App\Models\Operation;

use App\Models\Country;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Holiday
 *
 * @package App\Models\Operation
 * @property Carbon $date
 * @property int $id
 * @property int $country_id
 * @property \Carbon\Carbon $holiday_at
 * @property string|null $description
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 */
class Holiday extends Model
{
    use SoftDeletes;

    protected $table = 'operation_holidays';

    protected $fillable = ['holiday_at'];

    protected $casts = [
        'holiday_at' => 'date'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function scopeOfHolidayAtInRange($query, Carbon $from, Carbon $to)
    {
        return $query->whereBetween('operation_holidays.holiday_at', [$from->toDateString(), $to->toDateString()]);
    }

    public function scopeOfCountryId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('operation_holidays.country_id', $id);
        } else {
            return $query->where('operation_holidays.country_id', $id);
        }
    }

    public function isWeekday()
    {
        return $this->holiday_at->isWeekday();
    }

    public function isWeekend()
    {
        return $this->holiday_at->isWeekend();
    }
}