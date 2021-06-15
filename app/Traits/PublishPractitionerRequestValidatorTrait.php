<?php


namespace App\Traits;


use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait PublishPractitionerRequestValidatorTrait {

    private static string $adminRouteName = 'admin-practitioner-publish';

    public function validatePublishState($validator) {
        $validator->after(function($validator) {
            if ($this->route()->getName() === self::$adminRouteName) {
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
