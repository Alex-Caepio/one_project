<?php

namespace App\Http\Requests\Reschedule;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeclineRescheduleRequestRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return $this->requested_by === User::ACCOUNT_PRACTITIONER
            ? $this->booking->user_id === Auth::id()
            : $this->booking->practitioner_id === Auth::id();
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
