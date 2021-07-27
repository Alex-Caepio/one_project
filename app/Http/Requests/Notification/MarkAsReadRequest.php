<?php

namespace App\Http\Requests\Notification;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MarkAsReadRequest extends FormRequest {
    /**
     * Authorization rules
     *
     * @return bool
     */
    public function authorize() {
        return $this->notification->receiver_id === Auth::user()->id
               && !$this->notification->read_at;
    }

    /**
     * @return array
     */
    public function rules() {
        return [];
    }

}
