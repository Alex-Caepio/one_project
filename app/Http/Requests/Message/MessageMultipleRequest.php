<?php

namespace App\Http\Requests\Message;

use App\Http\Requests\Request;
use App\Models\Booking;

class MessageMultipleRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return $this->user()->is_admin || $this->user->isPractitioner();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'text'  => 'required|max:1000',
            'users' => 'required|array'
        ];
    }

}
