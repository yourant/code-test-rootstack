<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Repositories\SortingGateRepository;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class EditSortingGateRequest extends FormRequest
{
    

    /**
    * @var SortingGateRepository
    */
    protected $sortingGateRepository;


    public function __construct(SortingGateRepository $sortingGateRepository)
    {
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
        return [
            'code' => 'required|max:8|regex:/^[A-Z0-9]+$/',
            'number' => 'required|integer'
        ];
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

        if ($error = $this->validateUniqueGateCode()) {
            $errors[] = $error;
        }

        if ($error = $this->validateUniqueGateNumber()) {
            $errors[] = $error;
        }

        if ($errors) {
            throw new HttpResponseException($this->response($errors));
        }

    }

    public function validateUniqueGateCode()
    {

        $sorting_id =  $this->route('sorting');

        $gate_id = $this->route('gate');

        $code = $this->get('code');

        $gateCode = $this->sortingGateRepository->getByCode($sorting_id,$code);

        if (count($gateCode) && $gateCode->id != $gate_id) {
            return "The sorting already has a gate with the code {$code}";
        }
        
        return null;
    }

    public function validateUniqueGateNumber()
    {
        $sorting_id =  $this->route('sorting');

        $gate_id = $this->route('gate');

        $number = $this->get('number');

        $gateNumber = $this->sortingGateRepository->getByNumber($sorting_id,$number);

        if (count($gateNumber) && $gateNumber->id != $gate_id) {
            return "The sorting already has a gate with the number {$number}";
        }
        
        return null;
    }
}
