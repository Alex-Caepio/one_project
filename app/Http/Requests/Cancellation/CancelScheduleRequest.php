<?php

namespace App\Http\Requests\Cancellation;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * @property Booking booking
 */
class CancelScheduleRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $service = $this->schedule->service;
        return Auth::user()->is_admin || $service->user_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [];
    }

    public function withValidator($validator) {
        $validator->after(function($validator) {
            if ($this->schedule->start_date && Carbon::parse($this->schedule->start_date) < Carbon::now()) {
                $validator->errors()->add('error', 'The schedule cannot be cancelled');
            }
        });
    }

}
