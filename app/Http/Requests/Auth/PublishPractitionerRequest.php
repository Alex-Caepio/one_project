<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PublishPractitionerRequest extends Request {

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
        $practitioner =
            $this->route()->getName() === UpdateMediaRequest::ADMIN_ROUTE_NAME ? $this->practitioner : Auth::user();
        if ($practitioner instanceof User) {
            $validator->setRules([
                                     'business_name'               => 'required|max:255|min:2',
                                     'business_address'            => 'required|max:255',
                                     'business_email'              => 'required|max:255|email',
                                     'business_city'               => 'required|string|max:150',
                                     'business_phone_number'       => 'required|digits_between:2,255|numeric',
                                     'business_phone_country_code' => 'required|exists:countries,id|integer',
                                     'business_country_id'         => 'required|exists:countries,id|integer'
                                 ])->setData([
                                                 'business_address'            => $practitioner->business_address,
                                                 'business_email'              => $practitioner->business_email,
                                                 'business_phone_number'       => $practitioner->business_phone_number,
                                                 'business_phone_country_code' => $practitioner->business_phone_country_code,
                                                 'business_name'               => $practitioner->business_name,
                                                 'business_city'               => $practitioner->business_city,
                                                 'business_country_id'         => $practitioner->business_country_id,
                                             ])->validate();
        } else {
            abort(403, 'Practitioner is not defined');
        }
    }
}

