<?php namespace App\Validators;

class PackageUploadFormValidator extends AbstractValidator
{

    /**
     * Validation rules
     */
    public static $rules = array(
        'code'         => 'required|size:12',
        'file'         => 'required|max:5000',
    );

}