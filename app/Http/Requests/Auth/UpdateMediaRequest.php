<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateMediaRequest extends Request {

    public const ADMIN_ROUTE_NAME = 'admin-practitioner-publish';

    /**
     * Authorization rules
     *
     * @return bool
     */
    public function authorize() {
        return Auth::user()->isPractitioner() || Auth::user()->id_admin;
    }

    /**
     * @return array
     */
    public function rules() {
        $practitioner = $this->getPractitioner();
        return [
            'about_my_business'     => 'max:15000',
            'avatar_url'            => 'nullable|url',
            'background_url'        => 'nullable|url',
            'business_city'         => 'required_if:is_published,true|string|max:150',
            'business_name'         => 'required_if:is_published,true|max:255|min:2',
            'business_introduction' => 'required_if:is_published,true|string|max:300',
            'business_country_id'   => 'required|exists:countries,id|integer',
            'slug'                  => [
                Rule::unique('users', 'slug')->ignore($practitioner->id),
            ],
            'is_published'          => 'required|bool',
        ];
    }


    public function withValidator($validator) {
        $practitioner = $this->getPractitioner();
        if ($practitioner instanceof User) {
            $validator->setRules([
                                     'business_address'            => 'required|max:255',
                                     'business_email'              => 'required|max:255|email',
                                     'business_phone_number'       => 'required|digits_between:2,255|numeric',
                                     'business_phone_country_code' => 'required|exists:countries,id|integer',
                                 ])->setData([
                                                 'business_address'            => $practitioner->business_address,
                                                 'business_email'              => $practitioner->business_email,
                                                 'business_phone_number'       => $practitioner->business_phone_number,
                                                 'business_phone_country_code' => $practitioner->business_phone_country_code,
                                             ])->validate();

        } else {
            abort(403, 'Practitioner is not defined');
        }
    }


    private function getPractitioner() {
        if ($this->route()->getName() === self::ADMIN_ROUTE_NAME) {
            return $this->practitioner;
        }
        return Auth::user();
    }

}
