<?php

namespace App\Repositories;

use App\Models\Checkpoint;
use App\Models\ProviderService;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Collection;

class CheckpointRepository extends AbstractRepository
{

    function __construct(Checkpoint $model)
    {
        $this->model = $model;
    }

    public function search($params = [], $count = false)
    {
        $query = $this->model
            ->select('checkpoints.*')
            ->distinct()
            ->join('checkpoint_codes', 'checkpoints.checkpoint_code_id', '=', 'checkpoint_codes.id')
            ->join('packages', 'checkpoints.package_id', '=', 'packages.id')
            ->join('checkpoints as first_checkpoints', 'packages.first_checkpoint_id', '=', 'first_checkpoints.id');

        $joins = collect();

        if (isset($params['first_checkpoint_newer_than']) && $params['first_checkpoint_newer_than']) {
            $query->where('first_checkpoints.checkpoint_at', '>=', $params['first_checkpoint_newer_than']);
        }

        if (isset($params['first_checkpoint_older_than']) && $params['first_checkpoint_older_than']) {
            $query->where('first_checkpoints.checkpoint_at', '<=', $params['first_checkpoint_older_than']);
        }

        if (isset($params['client_id'])) {
            $this->addJoin($joins, 'agreements', 'agreements.id', 'packages.agreement_id');

            $query->where('agreements.client_id', '=', $params['client_id']);
        }

        if (isset($params['country_id'])) {
            $this->addJoin($joins, 'agreements', 'agreements.id', 'packages.agreement_id');
            $this->addJoin($joins, 'services', 'services.id', 'agreements.service_id');
            $this->addJoin($joins, 'locations as destination_location', 'destination_location.id', 'services.destination_location_id');

            $query->where('destination_location.country_id', '=', $params['country_id']);
        }

        if (isset($params['checkpoint_code_id']) && $params['checkpoint_code_id']) {
            $query->where('checkpoints.checkpoint_code_id', '=', $params['checkpoint_code_id']);
        }

        if (isset($params['finished']) && $params['finished']) {
            $query->where(function ($q2) {
                return $q2
                    ->where('packages.delivered', true)
                    ->orWhere('packages.returned', true)
                    ->orWhere('packages.canceled', true);
            });
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });


        return $count ? $query->count('checkpoints.id') : $query->orderBy('checkpoints.checkpoint_at', 'asc');
    }

    public function searchLatest($params = [])
    {
        $filters = collect($params);

        $query = $this->model
            ->select('checkpoints.*');

        if ($client_id = $filters->get('client_id')) {
            $query
                ->join('packages', 'checkpoints.package_id', '=', 'packages.id')
                ->join('agreements', 'packages.agreement_id', '=', 'agreements.id');

            if (is_array($client_id) && !empty($client_id)) {
                $query->whereIn('agreements.client_id', $client_id);
            } elseif ($client_id) {
                $query->where('agreements.client_id', $client_id);
            }
        }

        if ($from = $filters->get('from')) {
            $query->ofCreatedAtNewerThan($from);
        }

        if ($to = $filters->get('to')) {
            $query->ofCreatedAtOlderThan($to);
        }

        return $query->orderBy('checkpoints.created_at', 'desc');
    }

    public function getDistributionStatistics(ProviderService $providerService, $params)
    {
        $query = $this->buildDistributionStatisticsQuery($providerService, $params);

        return DB::table(DB::raw("({$query->toSql()}) as sub"))
            ->mergeBindings($query->getQuery())
            ->selectRaw(DB::raw('min(transit) as min, max(transit) as max, avg(transit) as avg, count(package_id) as package_count, sum(checkpoint_count) as checkpoint_count, sum(weight) as weight, avg(weight) as avg_weight'))
            ->first();
    }

    public function buildDistributionStatisticsQuery(ProviderService $providerService, $params)
    {
        $query = $this->model
            ->select(DB::raw('packages.id as package_id, packages.weight as weight, date_part(\'day\',max(checkpoints.checkpoint_at) - min(checkpoints.checkpoint_at)) as transit, count(checkpoints.id) as checkpoint_count'))
            ->join('packages', 'checkpoints.package_id', '=', 'packages.id')
            ->join('agreements', 'agreements.id', 'packages.agreement_id')
            ->join('services', 'services.id', 'agreements.service_id')
            ->join('locations as destination_location', 'destination_location.id', 'services.destination_location_id')
            ->join('checkpoint_codes', 'checkpoints.checkpoint_code_id', '=', 'checkpoint_codes.id');

        // Client
        if (isset($params['client_id']) && $params['client_id']) {
            $client_id = $params['client_id'];
            if (is_array($client_id) && !empty($client_id)) {
                $query->whereIn('agreements.client_id', $client_id);
            } else {
                $query->where('agreements.client_id', $client_id);
            }
        }

        // Marketplace
        if (isset($params['marketplace_id']) && $params['marketplace_id']) {
            $query->join('clients', 'agreements.client_id', '=', 'clients.id');
            $query->join('client_marketplace', 'client_marketplace.client_id', '=', 'clients.id');
            $marketplace_id = $params['marketplace_id'];
            if (is_array($marketplace_id) && !empty($marketplace_id)) {
                $query->whereIn('client_marketplace.marketplace_id', $marketplace_id);
            } else {
                $query->where('client_marketplace.marketplace_id', $marketplace_id);
            }
        }

        // From
        if (isset($params['first_checkpoint_newer_than']) && $params['first_checkpoint_newer_than']) {
            $query->where('packages.first_checkpoint_at', '>=', $params['first_checkpoint_newer_than']);
        }

        // To
        if (isset($params['first_checkpoint_older_than']) && $params['first_checkpoint_older_than']) {
            $query->where('packages.first_checkpoint_at', '<=', $params['first_checkpoint_older_than']);
        }

        // Search delivered, stalled or returning
        if (isset($params['distribution']) && $params['distribution']) {
            $query->where(function ($q2) {
                return $q2
                    ->where('packages.delivered', true)
                    ->orWhere('packages.stalled', true)
                    ->orWhere('packages.returning', true);
            });
        }

        // Provider
        $query->where('checkpoint_codes.provider_id', $providerService->provider_id);

        // Service type
//        $query->where('agreements.type', $serviceType->service);

        // Country
        $query->where('destination_location.country_id', $providerService->getProviderCountryId());

        // Order and grouping
        $query->groupBy('packages.id', 'packages.weight')->orderBy('transit');

        return $query;
    }

    public function getDistributionTransitDaysFrequency(ProviderService $providerService, $params)
    {
        $query = $this->buildDistributionStatisticsQuery($providerService, $params);

        return DB::table(DB::raw("({$query->toSql()}) as sub"))
            ->mergeBindings($query->getQuery())
            ->selectRaw(DB::raw('transit, count(*) as frequency'))
            ->groupBy('transit')
            ->get();
    }

    public function getTransitDaysStatsByProviderService(ProviderService $providerService, $params, $with_states = true)
    {
        $query = $this->getTransitDaysByProviderServiceQuery($providerService, $params, $with_states);

        return DB::table(DB::raw("({$query->toSql()}) as sub"))
            ->mergeBindings($query->getQuery())
            ->selectRaw(DB::raw('min(transit) as min, max(transit) as max, avg(transit) as avg, count(package_count) as package_count, count(checkpoint_count) as checkpoint_count, sum(weight) as weight, avg(weight) as avg_weight'))
            ->first();
    }

    public function getTransitDaysByProviderServiceQuery(ProviderService $providerService, $params, $with_states = true)
    {
        $query = $this->model
            ->join('checkpoint_codes', 'checkpoints.checkpoint_code_id', '=', 'checkpoint_codes.id')
            ->join('packages', 'checkpoints.package_id', '=', 'packages.id')
            ->join('delivery_routes', 'delivery_routes.id', '=', 'packages.delivery_route_id')
            ->join('legs', 'legs.delivery_route_id', '=', 'delivery_routes.id')
            ->join('provider_services', 'provider_services.id', '=', 'legs.provider_service_id');

        if ($with_states) {
            $query
                ->select(DB::raw('date_part(\'day\',max(checkpoints.checkpoint_at) - min(checkpoints.checkpoint_at)) as transit, count(packages.id) as package_count, count(checkpoints.id) as checkpoint_count, sum(packages.weight) as weight, admin_level_1.name as state_name, regions.name as region_name'))
                ->join('zip_codes', 'packages.zip_code_id', '=', 'zip_codes.id', 'left outer')
                ->join('admin_level_3', 'zip_codes.admin_level_3_id', '=', 'admin_level_3.id', 'left outer')
                ->join('admin_level_2', 'admin_level_3.admin_level_2_id', '=', 'admin_level_2.id', 'left outer')
                ->join('admin_level_1', 'admin_level_2.admin_level_1_id', '=', 'admin_level_1.id', 'left outer')
                ->join('regions', 'admin_level_1.region_id', '=', 'regions.id', 'left outer');
        } else {
            $query->select(DB::raw('date_part(\'day\',max(checkpoints.checkpoint_at) - min(checkpoints.checkpoint_at)) as transit, count(packages.id) as package_count, count(checkpoints.id) as checkpoint_count, sum(packages.weight) as weight'));
        }

        $query
            ->whereRaw('provider_services.provider_id = checkpoint_codes.provider_id')
            ->where('provider_services.id', $providerService->id);

        if (isset($params['finished']) && ($params['finished'])) {
            $query->where(function ($q2) {
                return $q2
                    ->where('packages.delivered', true)
                    ->orWhere('packages.returned', true)
                    ->orWhere('packages.canceled', true);
            });
        }

        if (isset($params['first_checkpoint_newer_than']) && $params['first_checkpoint_newer_than']) {
            $query->where('packages.first_checkpoint_at', '>=', $params['first_checkpoint_newer_than']);
        }

        if (isset($params['first_checkpoint_older_than']) && $params['first_checkpoint_older_than']) {
            $query->where('packages.first_checkpoint_at', '<=', $params['first_checkpoint_older_than']);
        }

        if (isset($params['admin_level_1_id']) && $params['admin_level_1_id']) {
            $query->where('admin_level_1.id', $params['admin_level_1_id']);
        }

        if (isset($params['client_id'])) {
            $query->whereIn('agreements.client_id', $params['client_id']);
        }

        if (isset($params['country_id'])) {
            $this->addJoin($joins, 'agreements', 'agreements.id', 'packages.agreement_id');
            $this->addJoin($joins, 'services', 'services.id', 'agreements.service_id');
            $this->addJoin($joins, 'locations as destination_location', 'destination_location.id', 'services.destination_location_id');

            $query->whereIn('destination_location.country_id', $params['country_id']);
        }

        if (isset($params['delayed']) && $params['delayed']) {
            if ((isset($params['unfinished']) && $params['unfinished']) or (isset($params['unaccomplished']) && $params['unaccomplished'])) {
                $query->whereRaw(DB::raw('date_part(\'day\',now() - packages.first_checkpoint_at) > agreements.controlled_transit_days'));
            } elseif ((isset($params['finished']) && $params['finished']) or (isset($params['accomplished']) && $params['accomplished'])) {
                $query->whereRaw(DB::raw('date_part(\'day\',packages.last_checkpoint_at - packages.first_checkpoint_at) > agreements.controlled_transit_days'));
            }
        }

        $query
            ->groupBy('packages.id')
            ->groupBy('admin_level_1.name')
            ->groupBy('regions.name')
            ->orderBy('transit');

        return $query;
    }

    public function getTransitDaysStatsByProviderServiceGroupedByStateName(ProviderService $providerService, $params)
    {
        $query = $this->getTransitDaysByProviderServiceQuery($providerService, $params);

        return DB::table(DB::raw("({$query->toSql()}) as sub"))
            ->mergeBindings($query->getQuery())
            ->selectRaw(DB::raw('min(transit) as min, max(transit) as max, avg(transit) as avg, count(package_count) as package_count, count(checkpoint_count) as checkpoint_count, sum(weight) as weight, avg(weight) as avg_weight, state_name'))
            ->groupBy('state_name')
            //->orderByRaw('ISNULL(state_name), state_name asc')
            ->orderByRaw('COALESCE(state_name, \'state_name asc\')')
            ->get();
    }

    public function getTransitDaysStatsByProviderServiceGroupedByRegion(ProviderService $providerService, $params)
    {
        $query = $this->getTransitDaysByProviderServiceQuery($providerService, $params);

        return DB::table(DB::raw("({$query->toSql()}) as sub"))
            ->mergeBindings($query->getQuery())
            ->selectRaw(DB::raw('min(transit) as min, max(transit) as max, avg(transit) as avg, count(package_count) as package_count, count(checkpoint_count) as checkpoint_count, sum(weight) as weight, avg(weight) as avg_weight, region_name'))
            ->groupBy('region_name')
            //->orderByRaw('ISNULL(region_name), region_name asc')
            ->orderByRaw('COALESCE(region_name, \'region_name asc\')')
            ->get();
    }

    public function getTransitDaysFrequencyByProviderService(ProviderService $providerService, $params)
    {
        $query = $this->getTransitDaysByProviderServiceQuery($providerService, $params, false);

        return DB::table(DB::raw("({$query->toSql()}) as sub"))
            ->mergeBindings($query->getQuery())
            ->selectRaw(DB::raw('transit, count(*) as frequency'))
            ->groupBy('transit')
            ->get();
    }

    public function getSyncedEventStatsByProviderServiceAndDateRange(ProviderService $providerService, $params, Carbon $from, $hourly = true, $manual = false)
    {
        $format = ($hourly) ? ' HH24:00:00' : '';
        $query = $this->model
            ->select(DB::raw('to_char(checkpoints.created_at, \'YYYY-mm-dd' . $format . '\') as event_date, COUNT(checkpoints.id) as frequency'))
            ->join('checkpoint_codes', 'checkpoints.checkpoint_code_id', '=', 'checkpoint_codes.id')
            ->join('providers', 'checkpoint_codes.provider_id', '=', 'providers.id')
//            ->join('service_types', 'service_types.provider_id', '=', 'providers.id')
            ->join('provider_services', 'provider_services.provider_id', '=', 'providers.id')
            ->where('provider_services.id', $providerService->id)
            ->where('checkpoints.manual', $manual)
            ->where('checkpoints.created_at', '>', $from->toDateTimeString());

        $query
            ->groupBy('checkpoints.created_at')
            ->orderBy('checkpoints.created_at');

        return DB::table(DB::raw("({$query->toSql()}) as sub group by event_date order by event_date asc"))
            ->mergeBindings($query->getQuery())
            ->selectRaw(DB::raw('event_date, sum(frequency) as frequency'))
            ->get();
    }

    public function searchPossibleValidDateInReceivedByField()
    {
        $query = $this->model
            ->select([
                'checkpoints.id',
                'checkpoints.package_id',
                'packages.tracking_number',
                'checkpoints.checkpoint_at',
                'checkpoints.created_at',
                'checkpoints.timezone_id',
                'checkpoints.received_by'
            ])
            ->join('packages', 'checkpoints.package_id', '=', 'packages.id')
            ->join('checkpoint_codes', 'checkpoints.checkpoint_code_id', '=', 'checkpoint_codes.id')
            ->where('checkpoint_codes.provider_id', 1)
            ->whereNotNull('checkpoints.received_by')
            ->where('checkpoints.checkpoint_at', '>=', '2017-01-01')
            ->where(function ($query) {
                return $query
                    ->where('checkpoints.received_by', '~', '[\s]+[\d]{1,2}\.[\d]{1,2}\.[\d]{1,4}')// 02.04.2018 o 2.4.2018 o 02.04.18 o 2.4.18
                    ->orWhere('checkpoints.received_by', '~', '[\s]+[\d]{1,2}\-[\d]{1,2}\-[\d]{1,4}')// 02-04-2018 o 2-4-2018 o 02-04-18 o 2-4-18
                    ->orWhere('checkpoints.received_by', '~', '[\s]+[\d]{1,2}\/[\d]{1,2}\/[\d]{1,4}')// 02/04/2018 o 2/4/2018 o 02/04/18 o 2/4/18
                    ->orWhere('checkpoints.received_by', '~', '[\s]+[\d]{1,2}[\s]+[\d]{1,2}[\s]+[\d]{1,4}')// 02 04 2018 o 2 4 2018 o 02 04 18 o 2 4 18
                    ->orWhere('checkpoints.received_by', '~', '[\s]+[\d]{1,2}\/[\w]{1,10}\/[\d]{1,4}')// 02/SEP/2018 o 02/SEPTIEMBRE/2018 o 2/SEP/18
                    ->orWhere('checkpoints.received_by', '~', '[\s]+[\d]{1,2}\-[\w]{1,10}\-[\d]{1,4}')// 02-SEP-2018 o 02-SEPTIEMBRE-2018 o 2-SEP-18
                    ->orWhere('checkpoints.received_by', '~', '[\s]+[\d]{1,2}\.[\d]{1,2}')// 02.04
                    ->orWhere('checkpoints.received_by', '~', '[\s]+[\d]{1,2}\-[\d]{1,2}')// 02-04
                    ->orWhere('checkpoints.received_by', '~', '[\s]+[\d]{1,2}\/[\d]{1,2}')// 02/04
                    ->orWhere('checkpoints.received_by', '~', '[\s]+[\d]{1,2}[\s]+[\d]{1,2}[\s]+[\d]{1,4}')// 02 04 2018 o 2 4 2018 o 02 04 18 o 2 4 18
                    ->orWhere('checkpoints.received_by', '~', '[\s]+[\d]{6}')// 020418
                    ->orWhere('checkpoints.received_by', '~', '[\s]+[\d]{8}'); // 02042018
            })
            ->orderBy('checkpoints.created_at', 'desc');

        return $query;
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }
}