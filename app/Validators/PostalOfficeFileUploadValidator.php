<?php namespace App\Validators;

class PostalOfficeFileUploadValidator extends AbstractValidator {

    /**
     * Validation rules
     */
    public static $rules = array(
        'file' => 'required'
    );

}