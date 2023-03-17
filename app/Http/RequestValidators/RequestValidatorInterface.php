<?php

namespace App\Http\RequestValidators;

use Illuminate\Validation\Validator;

interface RequestValidatorInterface
{
    public function validate(Validator $validator): Validator;
}
