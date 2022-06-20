<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email'      => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user()->id)
            ],
            'password'   => 'required|max:20|min:8|regex:/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]/',
            'first_name' => 'required|string|min:2|max:30',
            'last_name'  => 'required|string|min:2|max:30',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'   => 'Email is not available',
            'password.regex' => 'The password must include both uppercase and lowercase letters and at least one number'
        ];
    }
}
