<?php
namespace App\Validators;

class ClientValidator extends AbstractValidator {

    /**
     * Validation rules
     */
    public static $rules = array(
        'name' => 'required|min:3',
        'country_id' => 'required|exists:countries,id',
        'timezone_id' => 'required|exists:timezones,id'
    );

}