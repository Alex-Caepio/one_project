<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Request;

class PublishPractitionerRequest extends Request {

    public const ADMIN_ROUTE_NAME = 'admin-practitioner-publish';

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
        $validator->after(function($validator) {
            if ($this->route()->getName() === self::ADMIN_ROUTE_NAME) {
                $practitioner = $this->practitioner;
            } else {
                $practitioner = Auth::user();
            }
            if ($practitioner instanceof User) {
                $validator->setRules([
                                         'business_name'               => 'required|max:255|min:2',
                                         'business_address'            => 'required|max:255',
                                         'business_email'              => 'required|max:255|email',
                                         'business_city'               => 'required|string|max:150',
                                         'business_phone_number'       => 'required|digits_between:2,255|numeric',
                                         'business_phone_country_code' => 'required|exists:countries,id|integer',
                                         'business_country_id'         => 'required|exists:countries,id|integer'
                                     ])->setData($practitioner->toArray())->validate();
            } else {
                abort(403, 'Practitioner is not defined');
            }

        });
    }

}
