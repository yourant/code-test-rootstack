<?php namespace App\Validators;

class AddCheckpointValidator extends AbstractValidator {

    /**
     * Validation rules
     */
    public static $rules = array(
        'checkpoint_code_id' => 'required|exists:checkpoint_codes,id',
        'checkpoint_at'      => 'required|date|after:2015-01-01',
        'timezone_id'        => 'required|exists:timezones,id'
    );

}