<?php


namespace app\Http\Requests\Admin;


use App\Http\Requests\Request;

class UserDestroyRequest extends Request {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'message' => 'required|min:10|max:1000'
        ];
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages() {
        return [
            'message.required' => 'Message field is required',
            'message.min'      => 'Message field should be at least 10 symbols',
            'message.max'      => 'Message length must not be greater than 1000 symbols'
        ];
    }
}
