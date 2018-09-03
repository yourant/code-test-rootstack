<?php

namespace App\Http\Controllers\Api\Transformers;

use App\Models\SortingGate;
use League\Fractal\TransformerAbstract;

class SortingGateTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(SortingGate $sortingGate)
    {
        return [
            'id'      => $sortingGate->id,
            'name'    => $sortingGate->name,
            'number'  => $sortingGate->number,
            'default' => $sortingGate->default,
            'code'    => $sortingGate->code,
        ];
    }
}