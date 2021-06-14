<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Traits\PublishPractitionerRequestValidatorTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Validator;

class PublishPractitionerRequest extends Request {

    use PublishPractitionerRequestValidatorTrait;

    /**
     * Authorization rules
     *
     * @return bool
     */
    public function authorize() {
        return Auth::user()->is_admin || Auth::user()->isPractitioner();
    }

    /**
     * @return array
     */
    public function rules() {
        return [];
    }


    public function withValidator(Validator $validator) {
       $this->validatePublishState($validator);
    }

}
