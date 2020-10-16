<?php

namespace App\Http\Requests\Auth;


use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Authorization rules
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'current_password'        => 'required_with:password',
            'password'                => 'max:20|min:8|regex:/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]/',
            'first_name'              => 'string|min:2|max:30',
            'last_name'               => 'string|min:2|max:30',
        ];
    }

    public function messages()
    {
        return [
            'password.regex' => 'The password must include both uppercase and lowercase letters and at least one number'
        ];

    }

    public function withValidator($validator)
    {
        $user = $this->user();
        $validator->after(function ($validator) use ($user) {
            if ($this->get('current_password') && !Hash::check($this->get('current_password'), $user->password)) {
                $validator->errors()->add('current_password', 'The current password is incorrect!');
                return;
            }
        });
    }
}
