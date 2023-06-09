<?php

namespace App\Http\Requests\Services;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class ServiceOwnerRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return $this->service->user_id === Auth::id() || Auth::user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [];
    }
}
