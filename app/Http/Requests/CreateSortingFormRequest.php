<?php

namespace App\Http\Requests;

use App\Models\SortingType;
use App\Repositories\SortingTypeRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateSortingFormRequest extends FormRequest
{
    /** @var SortingTypeRepository */
    protected $sortingTypeRepository;

    public function __construct(SortingTypeRepository $sortingTypeRepository)
    {
        $this->sortingTypeRepository = $sortingTypeRepository;
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
        return [
            'name'            => 'required',
            'sorting_type_id' => 'required|array|min:1'
        ];
    }

    public function validate()
    {
        parent::validate();

        $errors = [];

        if ($this->geographicSortingTypeCount() > 1) {
            array_push($errors, 'Different types of geographic location sortings can not be combined');
        }

        if ($errors) {
            throw new HttpResponseException($this->response($errors));
        }
    }

    public function geographicSortingTypeCount()
    {
        $geographical_count = 0;

        $sortingTypeIds = $this->get('sorting_type_id');
        if (is_array($sortingTypeIds) && !empty($sortingTypeIds)) {
            foreach ($sortingTypeIds as $sortingTypeId) {
                /** @var SortingType $st */
                $st = $this->sortingTypeRepository->getById($sortingTypeId);
                if ($st && $st->isGeographical()) {
                    $geographical_count++;
                }
            }
        }

        return $geographical_count;
    }
}
