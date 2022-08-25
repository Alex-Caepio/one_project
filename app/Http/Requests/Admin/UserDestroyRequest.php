<?php

namespace app\Http\Requests\Admin;

use App\Http\Requests\Request;

/**
 * @property-read string $message
 */
class UserDestroyRequest extends Request
{
    public function rules(): array
    {
        return [
            'message' => 'required|min:10|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Message field is required',
            'message.min'      => 'Message field should be at least 10 symbols',
            'message.max'      => 'Message length must not be greater than 1000 symbols',
        ];
    }
}
