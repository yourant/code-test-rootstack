<?php namespace App\Validators;

use Validator;

abstract class AbstractValidator {

    protected $input;
    protected $errors;

    public function __construct(array $input = null)
    {
        $this->input = $input ?: request()->all();
    }

    public function passes()
    {
        $validation = Validator::make($this->input, static::$rules);
        if($validation->passes())
            return true;

        $this->errors = $validation->messages();
        return false;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getInput()
    {
        return $this->input;
    }

}