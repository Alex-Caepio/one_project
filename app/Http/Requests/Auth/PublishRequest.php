<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PublishRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            if (!$user->business_name) {
                $validator->errors()->add(
                    'business_name',
                    'You have not filled in the field "Business name"'
                );
            }
            if (!$user->business_address) {
                $validator->errors()->add(
                    'business_address',
                    'You have not filled in the field "Business address"'
                );
            }
            if (!$user->business_email) {
                $validator->errors()->add(
                    'business_email',
                    'You have not filled in the field "Business email"'
                );
            }
            if (!$user->business_introduction) {
                $validator->errors()->add(
                    'business_introduction',
                    'You have not filled in the field "Business introduction"'
                );
            }
            if (!$user->timezone_id) {
                $validator->errors()->add(
                    'timezone_id',
                    'You have not filled in the field "Timezone"'
                );
            }

        });
    }

}
