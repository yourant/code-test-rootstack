<?php

namespace App\Repositories\Operation;

use App\Repositories\AbstractRepository;
use App\Models\Operation\Holiday;
use Carbon\Carbon;

class HolidayRepository extends AbstractRepository
{
    function __construct(Holiday $model)
    {
        $this->model = $model;
    }

    /**
     * @param Carbon $from
     * @param Carbon $to
     * @param array $country_ids
     * @return \Illuminate\Support\Collection
     */
    public function getBetweenDates(Carbon $from, Carbon $to, array $country_ids = [])
    {
        $query = $this->model
            ->select('operation_holidays.*')
            ->ofHolidayAtInRange($from, $to);

        if (!empty($country_ids)) {
            $query->ofCountryId($country_ids);
        }

        return $query->get();
    }

    /**
     * @param Carbon $from
     * @param Carbon $to
     * @param array $country_ids
     * @return int
     */
    public function countWeekdaysByRange(Carbon $from, Carbon $to, array $country_ids = [])
    {
        return $this->getBetweenDates($from, $to, $country_ids)->filter(function ($holiday) {
            return $holiday->isWeekday();
        })->unique('holiday_at')->count();
    }
}
