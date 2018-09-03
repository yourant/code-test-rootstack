<?php

namespace App\Repositories;

use App\Models\SortingGate;
use Illuminate\Support\Collection;

/**
 * Class SortingGateRepository
 * @package App\Repositories
 */
class SortingGateRepository extends AbstractRepository
{
    /**
     * SortingGateRepository constructor.
     * @param SortingGate $model
     */
    function __construct(SortingGate $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function filter(array $filters = [])
    {
        $query = $this->model->select('sorting_gates.*');

        $joins = collect();

        if (isset($filters['sorting_id']) && $filters['sorting_id']) {
            $query->ofSortingId($filters['sorting_id']);
        }

        if (isset($filters['zip_code']) && $filters['zip_code']) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $this->addJoin($joins, 'sorting_gate_criteria_zip_code', 'sorting_gate_criteria_zip_code.sorting_gate_criteria_id', 'sorting_gate_criterias.id');
            $this->addJoin($joins, 'zip_codes', 'zip_codes.id', 'sorting_gate_criteria_zip_code.zip_code_id');
            if (!empty($filters['zip_code']) && is_array($filters['zip_code'])) {
                $query->whereIn("zip_codes.code", $filters['zip_code']);
            } else {
                $query->where("zip_codes.code", $filters['zip_code']);
            }
        }

        if (isset($filters['postal_office']) && $filters['postal_office']) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $this->addJoin($joins, 'sorting_gate_criteria_zip_code', 'sorting_gate_criteria_zip_code.sorting_gate_criteria_id', 'sorting_gate_criterias.id');
            $this->addJoin($joins, 'zip_codes', 'zip_codes.id', 'sorting_gate_criteria_zip_code.zip_code_id');
            $this->addJoin($joins, 'postal_office_zip_code', 'zip_codes.id', 'sorting_gate_criteria_zip_code.zip_code_id');
            $this->addJoin($joins, 'postal_offices', 'postal_office_zip_code.postal_office_id', 'postal_offices.id');
            $query->where("postal_offices.name", "like", "%{$filters['postal_office']}%");
        }

        if (isset($filters['township']) && $filters['township']) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $this->addJoin($joins, 'sorting_gate_criteria_zip_code', 'sorting_gate_criteria_zip_code.sorting_gate_criteria_id', 'sorting_gate_criterias.id');
            $this->addJoin($joins, 'zip_codes', 'zip_codes.id', 'sorting_gate_criteria_zip_code.zip_code_id');
            $this->addJoin($joins, 'townships', 'townships.id', 'zip_codes.township_id');
            $query->where("townships.name", "like", "%{$filters['township']}%");
        }

        if (isset($filters['town']) && $filters['town']) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $this->addJoin($joins, 'sorting_gate_criteria_zip_code', 'sorting_gate_criteria_zip_code.sorting_gate_criteria_id', 'sorting_gate_criterias.id');
            $this->addJoin($joins, 'zip_codes', 'zip_codes.id', 'sorting_gate_criteria_zip_code.zip_code_id');
            $this->addJoin($joins, 'townships', 'townships.id', 'zip_codes.township_id');
            $this->addJoin($joins, 'towns', 'towns.id', 'townships.town_id');
            $query->where("towns.name", "like", "%{$filters['town']}%");
        }

        if (isset($filters['state']) && $filters['state']) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $this->addJoin($joins, 'sorting_gate_criteria_zip_code', 'sorting_gate_criteria_zip_code.sorting_gate_criteria_id', 'sorting_gate_criterias.id');
            $this->addJoin($joins, 'zip_codes', 'zip_codes.id', 'sorting_gate_criteria_zip_code.zip_code_id');
            $this->addJoin($joins, 'townships', 'townships.id', 'zip_codes.township_id');
            $this->addJoin($joins, 'towns', 'towns.id', 'townships.town_id');
            $this->addJoin($joins, 'states', 'states.id', 'towns.state_id');
            $query->where("states.name", "like", "%{$filters['state']}%");
        }

        if (isset($filters['region']) && $filters['region']) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $this->addJoin($joins, 'sorting_gate_criteria_zip_code', 'sorting_gate_criteria_zip_code.sorting_gate_criteria_id', 'sorting_gate_criterias.id');
            $this->addJoin($joins, 'zip_codes', 'zip_codes.id', 'sorting_gate_criteria_zip_code.zip_code_id');
            $this->addJoin($joins, 'townships', 'townships.id', 'zip_codes.township_id');
            $this->addJoin($joins, 'towns', 'towns.id', 'townships.town_id');
            $this->addJoin($joins, 'states', 'states.id', 'towns.state_id');
            $this->addJoin($joins, 'regions', 'regions.id', 'states.region_id');
            $query->where("regions.name", "like", "%{$filters['region']}%");
        }

        if (isset($filters['value']) && $filters['value']) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $query->where(function ($sub) use ($filters) {
                $sub->orWhere(function ($q) use ($filters) {
                    $q->where('sorting_gate_criterias.after_than', '>=', $filters['value']);
                    $q->where('sorting_gate_criterias.before_than', '<=', $filters['value']);
                });

                $sub->orWhere(function ($q) use ($filters) {
                    $q->where('sorting_gate_criterias.before_than', null);
                    $q->where(function ($qs) use ($filters) {
                        $qs->where('sorting_gate_criterias.after_than', '<', $filters['value']);
                        $qs->orWhere('sorting_gate_criterias.after_than', '<', $filters['value']);
                    });
                });

                $sub->orWhere(function ($q) use ($filters) {
                    $q->where('sorting_gate_criterias.after_than', null);
                    $q->where(function ($qs) use ($filters) {
                        $qs->where('sorting_gate_criterias.before_than', '>', $filters['value']);
                        $qs->orWhere('sorting_gate_criterias.before_than', '>', $filters['value']);
                    });
                });
            });
        }

        if (isset($filters['weight']) && $filters['weight']) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $query->where(function ($sub) use ($filters) {
                $sub->orWhere(function ($q) use ($filters) {
                    $q->where('sorting_gate_criterias.after_than', '>=', $filters['weight']);
                    $q->where('sorting_gate_criterias.before_than', '<=', $filters['weight']);
                });

                $sub->orWhere(function ($q) use ($filters) {
                    $q->where('sorting_gate_criterias.before_than', null);
                    $q->where(function ($qs) use ($filters) {
                        $qs->where('sorting_gate_criterias.after_than', '<', $filters['weight']);
                        $qs->orWhere('sorting_gate_criterias.after_than', '<', $filters['weight']);
                    });
                });

                $sub->orWhere(function ($q) use ($filters) {
                    $q->where('sorting_gate_criterias.after_than', null);
                    $q->where(function ($qs) use ($filters) {
                        $qs->where('sorting_gate_criterias.before_than', '>', $filters['weight']);
                        $qs->orWhere('sorting_gate_criterias.before_than', '>', $filters['weight']);
                    });
                });
            });
        }

        if (isset($filters['criteria_code']) && $filters['criteria_code']) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $query->where('criteria_code', $filters['criteria_code']);
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query;
    }

    public function getBySortingType(array $filters = [], $distinct = true)
    {
        //se busca el sorting
        $query = $this->filter($filters);

        if ($distinct) {
            $query->distinct();
        }
        // Se aplica logica para obtener la relacion geografica
        $joins = collect();

        if (isset($filters['region_id']) && $filters['region_id']) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $this->addJoin($joins, 'sorting_gate_criteria_zip_code', 'sorting_gate_criteria_zip_code.sorting_gate_criteria_id', 'sorting_gate_criterias.id');
            $this->addJoin($joins, 'zip_codes', 'zip_codes.id', 'sorting_gate_criteria_zip_code.zip_code_id');
            $this->addJoin($joins, 'admin_level_3', 'admin_level_3.id', 'zip_codes.admin_level_3_id');
            $this->addJoin($joins, 'admin_level_2', 'admin_level_2.id', 'admin_level_3.admin_level_2_id');
            $this->addJoin($joins, 'admin_level_1', 'admin_level_1.id', 'admin_level_2.admin_level_1_id');
            $this->addJoin($joins, 'regions', 'regions.id', 'admin_level_1.region_id');
            $query->whereIn('regions.id', $filters['region_id']);
        }

        if (isset($filters['state_id']) && $filters['state_id']) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $this->addJoin($joins, 'sorting_gate_criteria_zip_code', 'sorting_gate_criteria_zip_code.sorting_gate_criteria_id', 'sorting_gate_criterias.id');
            $this->addJoin($joins, 'zip_codes', 'zip_codes.id', 'sorting_gate_criteria_zip_code.zip_code_id');
            $this->addJoin($joins, 'admin_level_3', 'admin_level_3.id', 'zip_codes.admin_level_3_id');
            $this->addJoin($joins, 'admin_level_2', 'admin_level_2.id', 'admin_level_3.admin_level_2_id');
            $this->addJoin($joins, 'admin_level_1', 'admin_level_1.id', 'admin_level_2.admin_level_1_id');
            $query->whereIn('admin_level_1.id', $filters['state_id']);
        }

        if (isset($filters['town_id']) && $filters['town_id']) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $this->addJoin($joins, 'sorting_gate_criteria_zip_code', 'sorting_gate_criteria_zip_code.sorting_gate_criteria_id', 'sorting_gate_criterias.id');
            $this->addJoin($joins, 'zip_codes', 'zip_codes.id', 'sorting_gate_criteria_zip_code.zip_code_id');
            $this->addJoin($joins, 'admin_level_3', 'admin_level_3.id', 'zip_codes.admin_level_3_id');
            $this->addJoin($joins, 'admin_level_2', 'admin_level_2.id', 'admin_level_3.admin_level_2_id');
            $query->whereIn('admin_level_2.id', $filters['town_id']);
        }

        if (isset($filters['postal_office_id']) && $filters['postal_office_id']) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $this->addJoin($joins, 'sorting_gate_criteria_zip_code', 'sorting_gate_criteria_zip_code.sorting_gate_criteria_id', 'sorting_gate_criterias.id');
            $this->addJoin($joins, 'zip_codes', 'zip_codes.id', 'sorting_gate_criteria_zip_code.zip_code_id');
            $this->addJoin($joins, 'postal_office_zip_code', 'postal_office_zip_code.zip_code_id', 'zip_codes.id');
            $this->addJoin($joins, 'postal_offices', 'postal_offices.id', 'postal_office_zip_code.postal_office_id');
            $query->whereIn('postal_offices.id', $filters['postal_office_id']);
        }

        // Partial filters

        // By Weight filter
        if (isset($filters['is_weight_after_than']) && $filters['is_weight_after_than'] && isset($filters['is_weight_before_than']) && $filters['is_weight_before_than']) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $query->where(function ($sub) use ($filters) {
                $sub->where(function ($q) use ($filters) {
                    $q->whereBetween('sorting_gate_criterias.after_than', [$filters['is_weight_after_than'], $filters['is_weight_before_than']]);
                    $q->orWhereBetween('sorting_gate_criterias.before_than', [$filters['is_weight_after_than'], $filters['is_weight_before_than']]);
                });

                $sub->orWhere(function ($q) use ($filters) {
                    $q->where('sorting_gate_criterias.after_than', '>=', $filters['is_weight_after_than']);
                    $q->where('sorting_gate_criterias.before_than', '<=', $filters['is_weight_after_than']);
                });

                $sub->orWhere(function ($q) use ($filters) {
                    $q->where('sorting_gate_criterias.after_than', '>=', $filters['is_weight_before_than']);
                    $q->where('sorting_gate_criterias.before_than', '<=', $filters['is_weight_before_than']);
                });

                $sub->orWhere(function ($q) use ($filters) {
                    $q->where('sorting_gate_criterias.before_than', null);
                    $q->where(function ($qs) use ($filters) {
                        $qs->where('sorting_gate_criterias.after_than', '<', $filters['is_weight_after_than']);
                        $qs->orWhere('sorting_gate_criterias.after_than', '<', $filters['is_weight_before_than']);
                    });
                });

                $sub->orWhere(function ($q) use ($filters) {
                    $q->where('sorting_gate_criterias.after_than', null);
                    $q->where(function ($qs) use ($filters) {
                        $qs->where('sorting_gate_criterias.before_than', '>', $filters['is_weight_after_than']);
                        $qs->orWhere('sorting_gate_criterias.before_than', '>', $filters['is_weight_before_than']);
                    });
                });
            });
        }

        if (isset($filters['is_weight_after_than']) && $filters['is_weight_after_than'] && !isset($filters['is_weight_before_than'])) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $query->where(function ($sub) use ($filters) {
                $sub->where('sorting_gate_criterias.after_than', '>', $filters['is_weight_after_than']);
                $sub->orWhere('sorting_gate_criterias.before_than', '>', $filters['is_weight_after_than']);
            });
        }

        if (isset($filters['is_weight_before_than']) && $filters['is_weight_before_than'] && !isset($filters['is_weight_after_than'])) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $query->where(function ($sub) use ($filters) {
                $sub->where('sorting_gate_criterias.after_than', '<', $filters['is_weight_before_than']);
                $sub->orWhere('sorting_gate_criterias.before_than', '<', $filters['is_weight_before_than']);
            });
        }

        // By Value filter
        if (isset($filters['is_value_after_than']) && $filters['is_value_after_than'] && isset($filters['is_value_before_than']) && $filters['is_value_before_than']) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $query->where(function ($sub) use ($filters) {
                $sub->where(function ($q) use ($filters) {
                    $q->whereBetween('sorting_gate_criterias.after_than', [$filters['is_value_after_than'], $filters['is_value_before_than']]);
                    $q->orWhereBetween('sorting_gate_criterias.before_than', [$filters['is_value_after_than'], $filters['is_value_before_than']]);
                });

                $sub->orWhere(function ($q) use ($filters) {
                    $q->where('sorting_gate_criterias.after_than', '>=', $filters['is_value_after_than']);
                    $q->where('sorting_gate_criterias.before_than', '<=', $filters['is_value_after_than']);
                });

                $sub->orWhere(function ($q) use ($filters) {
                    $q->where('sorting_gate_criterias.after_than', '>=', $filters['is_value_before_than']);
                    $q->where('sorting_gate_criterias.before_than', '<=', $filters['is_value_before_than']);
                });

                $sub->orWhere(function ($q) use ($filters) {
                    $q->where('sorting_gate_criterias.before_than', null);
                    $q->where(function ($qs) use ($filters) {
                        $qs->where('sorting_gate_criterias.after_than', '<', $filters['is_value_after_than']);
                        $qs->orWhere('sorting_gate_criterias.after_than', '<', $filters['is_value_before_than']);
                    });
                });

                $sub->orWhere(function ($q) use ($filters) {
                    $q->where('sorting_gate_criterias.after_than', null);
                    $q->where(function ($qs) use ($filters) {
                        $qs->where('sorting_gate_criterias.before_than', '>', $filters['is_value_after_than']);
                        $qs->orWhere('sorting_gate_criterias.before_than', '>', $filters['is_value_before_than']);
                    });
                });
            });
        }

        if (isset($filters['is_value_after_than']) && $filters['is_value_after_than'] && !isset($filters['is_value_before_than'])) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $query->where(function ($sub) use ($filters) {
                $sub->where('sorting_gate_criterias.after_than', '>', $filters['is_value_after_than']);
                $sub->orWhere('sorting_gate_criterias.before_than', '>', $filters['is_value_after_than']);
            });
        }

        if (isset($filters['is_value_before_than']) && $filters['is_value_before_than'] && !isset($filters['is_value_after_than'])) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $query->where(function ($sub) use ($filters) {
                $sub->where('sorting_gate_criterias.after_than', '<', $filters['is_value_before_than']);
                $sub->orWhere('sorting_gate_criterias.before_than', '<', $filters['is_value_before_than']);
            });
        }

        if (isset($filters['is_criteria']) && $filters['is_criteria']) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $query->where('sorting_gate_criterias.criteria_code', $filters['is_criteria']);
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $query->where('sorting_gate_criterias.criteria_code', $filters['is_criteria']);
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query;
    }

    public function getCriteriaById(array $filters = [], $distinct = true)
    {
        $query = $this->filter($filters);

        if ($distinct) {
            $query->distinct();
        }

        $joins = collect();

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        if (isset($filters['id']) && $filters['id']) {
            $this->addJoin($joins, 'sorting_gate_criterias', 'sorting_gate_criterias.sorting_gate_id', 'sorting_gates.id');
            $this->addJoin($joins, 'sorting_gate_criteria_zip_code', 'sorting_gate_criteria_zip_code.sorting_gate_criteria_id', 'sorting_gate_criterias.id');
            $query->where("sorting_gates.id", $filters['id']);
        }

        return $query;
    }

    public function getByCode($sorting_id, $code)
    {
        $query = $this->filter(['sorting_id' => $sorting_id]);

        $query->where('sorting_gates.gate_code', $code);

        return $query->first();
    }

    public function getByNumber($sorting_id, $number)
    {
        return $this->filter(compact('sorting_id', 'number'))->first();
    }

    /**
     * @param Collection $joins
     * @param $table
     * @param $first
     * @param $second
     * @param string $join_type
     */
    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }
}
