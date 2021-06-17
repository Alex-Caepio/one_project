<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;
use App\Traits\PublishPractitionerRequestValidatorTrait;
use Illuminate\Support\Facades\Auth;

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


    public function withValidator($validator) {
       $this->validatePublishState($validator);
    }

}
