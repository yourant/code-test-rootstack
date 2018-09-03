<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Transformers\SortingGateTransformer;
use App\Http\Controllers\Controller;
use App\Models\Sorting;
use App\Repositories\SortingRepository;
use App\Services\Sortings\SortingGateClassificationService;
use App\Traits\JsonResponse;
use Exception;
use Illuminate\Http\Request;

class SortingGatesController extends Controller
{
    use JsonResponse;

    private $inputs = ['criteria_code', 'weight', 'value', 'zip_code', 'address', 'district', 'location', 'town', 'state'];

    /** @var \App\Repositories\SortingRepository */
    protected $sortingRepository;

    /** @var \App\Services\Sortings\SortingGateClassificationService */
    protected $sortingGateClassificationService;

    public function __construct(
        SortingRepository $sortingRepository,
        SortingGateClassificationService $sortingGateClassificationService,
        SortingGateTransformer $sortingGateTransformer
    ) {
        $this->sortingRepository = $sortingRepository;
        $this->sortingGateClassificationService = $sortingGateClassificationService;
    }

    public function search(Request $request)
    {
        $params = $request->all();
        logger('[Request] Sorting Gate');
        logger($params);

        if (!$request->has('service_code')) {
            return self::badRequest('Parameter missing: "service_code".');
        }

        $inputs = collect($params)->filter(function ($v, $k) {
            if (in_array($k, $this->inputs) && $v) {
                return true;
            }
        });

        if (!$inputs->count()) {
            return self::badRequest('No valid parameter is being received.');
        }

        $service_code = $request->get('service_code');

        /** @var Sorting $sorting */
        if (!$sorting = $this->sortingRepository->getByServiceCode($service_code)) {
            return self::badRequest('Service not found or is not associated with a sorting.');
        }

        try {
            $sorting->load(['sortingTypes']);

            $data = $this->sortingGateClassificationService->getGate($sorting, $inputs->toArray());

            $fractal = fractal($data['gate'], new SortingGateTransformer);
            $fractal->addMeta(['error' => false]);
            $fractal->addMeta(['errors' => $data['errors']]);

            $response = $fractal->toArray();
            logger('[Sorting] Response');
            logger($response);

            return 'a';

            return response()->json($response, 200, [], JSON_PRETTY_PRINT);
        } catch (Exception $e) {
            return self::internalServerError($e->getMessage());
        }
    }
}