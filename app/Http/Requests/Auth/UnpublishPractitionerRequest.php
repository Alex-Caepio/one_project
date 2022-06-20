<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UnpublishPractitionerRequest extends Request {
    /**
     * Authorization rules
     *
     * @return bool
     */
    public function authorize() {
        return Auth::user()->isPractitioner() && Auth::user()->is_published;
    }

    /**
     * @return array
     */
    public function rules() {
        return [
            'cancel_bookings' => 'required|bool',
        ];
    }

}
