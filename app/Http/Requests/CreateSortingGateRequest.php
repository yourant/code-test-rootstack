<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Repositories\SortingRepository;
use App\Repositories\SortingGateRepository;
use Illuminate\Support\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class CreateSortingGateRequest extends FormRequest
{

     /**
     * @var SortingGateRepository
     */
    protected $sortingRepository;

    /**
     * @var SortingGateRepository
     */
    protected $sortingGateRepository;


    public function __construct(SortingRepository $sortingRepository,
        SortingGateRepository $sortingGateRepository)
    {
        $this->sortingRepository = $sortingRepository;
        $this->sortingGateRepository = $sortingGateRepository;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'code'              => [
                    'required',
                    'max:8',
                    'regex:/^[a-z|0-9]+$/i'
                ],
            'criteria'          => 'max:255',
            'region_id'         => 'exists:regions,id',
            'state_id'          => 'exists:admin_level_1,id',
            'town_id'           => 'exists:admin_level_2,id',
            'postal_office_id'  => 'exists:postal_offices,id',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'code.regex' => 'Format for wrong code, should not carry special characters such as blank spaces, commas, points, among others.',
        ];
    }

    public function validate()
    {

        parent::validate();

        $errors = [];

        $sorting_id =  $this->route('sorting');

        $sorting = $this->sortingRepository->find($sorting_id);

        foreach ($sorting->sortingTypes as $sortingType)
        {
            if ($sortingType->isByValue()) {
                if ($error = $this->validateGateHasUniqueValue()) {
                    $errors[] = $error;
                }
            }

            if ($sortingType->isByRegion()) {
                if ($error = $this->validateGateHasUniqueRegion()) {
                    $errors[] = $error;
                }
            }

            if ($sortingType->isByState()) {
                if ($error = $this->validateGateHasUniqueState()) {
                    $errors[] = $error;
                }
            }

            if ($sortingType->isByTown()) {
                if ($error = $this->validateGateHasUniqueTown()) {
                    $errors[] = $error;
                }
            }

            if ($sortingType->isByWeight()) {
                if ($error = $this->validateGateHasUniqueWeight()) {
                    $errors[] = $error;
                }
            }

            if ($sortingType->isByCriteria()) {
                if ($error = $this->validateGateHasUniqueCriteria()) {
                    $errors[] = $error;
                }
            }

            if ($sortingType->isByPostalOffice()) {
                if ($error = $this->validateGateHasUniquePostalOffice()) {
                    $errors[] = $error;
                }
            }
        }

        if ($errors) {
            throw new HttpResponseException($this->response($errors));
        }

    }


    public function validateGateHasUniqueValue()
    {
        $sorting_id =  $this->route('sorting');
        $after_than = $this->get('after_value');
        $before_than = $this->get('before_value');
        if ((isset($after_than) && $after_than) || (isset($before_than) && $before_than)) {

            if($after_than && !is_numeric($after_than)) return "The after_value must be numeric";
            if($before_than && !is_numeric($before_than)) return "The before_value must be numeric";

            $filter = [
                'sorting_id' => $sorting_id,
            ];

            if($after_than) $filter['is_value_after_than'] = $after_than;
            if($before_than) $filter['is_value_before_than'] = $before_than;

            $value_validation = $this->sortingGateRepository->getBySortingType($filter)->first();

            if (count($value_validation)) {
                return "The sorting already has a gate with the sorting type by value range {$after_than} - {$before_than}";
            }
        } else if(empty($after_than) && !$after_than && empty($before_than) && !$before_than) {
            return "The after_value or before_value are required";
        }
        return false;

    }

    public function validateGateHasUniqueWeight()
    {
        $sorting_id =  $this->route('sorting');
        $after_than = $this->get('after_weight');
        $before_than = $this->get('before_weight');

        if ((isset($after_than) && $after_than) || (isset($before_than) && $before_than)) {

            if($after_than && !is_numeric($after_than)) return "The after_weight must be numeric";
            if($before_than && !is_numeric($before_than)) return "The before_weight must be numeric";

            $filter = [
                'sorting_id' => $sorting_id,
            ];

            if($after_than) $filter['is_weight_after_than'] = $after_than;
            if($before_than) $filter['is_weight_before_than'] = $before_than;

            $value_validation = $this->sortingGateRepository->getBySortingType($filter)->first();

            if (count($value_validation)) {
                return "The sorting already has a gate with the sorting type by weight range {$after_than} - {$before_than}";
            }
        } else if(empty($after_than) && !$after_than && empty($before_than) && !$before_than) {
            return "The after_weight or before_weight are required";
        }
        return false;
    }

    public function validateGateHasUniqueCriteria()
    {
        $sorting_id =  $this->route('sorting');
        $criteria = $this->get('criteria');

        if (isset($criteria) && $criteria) {

            $value_validation = $this->sortingGateRepository->getBySortingType([
                'sorting_id' => $sorting_id,
                'is_criteria' => $criteria
            ])->get();

            if (count($value_validation)) {
                return "The sorting already has a gate with the sorting type by weight range";
            }
        }elseif (empty($criteria) && !$criteria) {
            return "the criteria field is required";
        }
        return null;
    }

    public function validateGateHasUniqueRegion()
    {
        $sorting_id =  $this->route('sorting');
        $regionIds = $this->get('region_id');

        if (isset($regionIds) && $regionIds) {

            $value_validation = $this->sortingGateRepository->getBySortingType([
                'sorting_id' => $sorting_id,
                'region_id' => $regionIds
            ])->get();

            if (count($value_validation)) {
                return "The sorting already has a gate with the sorting type by region";
            }
        }elseif(empty($regionIds) && !$regionIds){
            return "the region field is required";
        }
        return null;
    }

    public function validateGateHasUniqueState()
    {
        $sorting_id =  $this->route('sorting');
        $stateIds = $this->get('state_id');

        if (isset($stateIds) && $stateIds) {

             $value_validation = $this->sortingGateRepository->getBySortingType([
                'sorting_id' => $sorting_id,
                'state_id' => $stateIds
            ])->get();

            if (count($value_validation)) {
                return "The sorting already has a gate with the sorting type by state";
            }
        }elseif(empty($stateIds) && !$stateIds){
            return "the state field is required";
        }
        return null;
    }

    public function validateGateHasUniqueTown()
    {
        $sorting_id =  $this->route('sorting');
        $cityIds = $this->get('town_id');

        if (isset($cityIds) && $cityIds) {

            $value_validation = $this->sortingGateRepository->getBySortingType([
                'sorting_id' => $sorting_id,
                'town_id' => $cityIds
            ])->get();

            if (count($value_validation)) {
                return "The sorting already has a gate with the sorting type by town";
            }
        }elseif (empty($cityIds) && !$cityIds){
            return "the town field is required";
        }
        return null;
    }

    public function validateGateHasUniquePostalOffice()
    {
        $sorting_id =  $this->route('sorting');
        $postalOfficeIds = $this->get('postal_office_id');

        if (isset($postalOfficeIds) && $postalOfficeIds) {

            $value_validation = $this->sortingGateRepository->getBySortingType([
                'sorting_id' => $sorting_id,
                'postal_office_id' => $postalOfficeIds
            ])->get();

            if (count($value_validation)) {
                return "The sorting already has a gate with the sorting type by postal office";
            }
        }elseif (empty($postalOfficeIds) && !$postalOfficeIds) {
            return "the postal office field is required";
        }
        return null;
    }

}
