<?php

namespace App\Http\Requests\Cancellation;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * @property Booking booking
 */
class CancelBookingRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $userBooking = Auth::id() == $this->booking->user_id;
        $practitionerBooking = Auth::id() == $this->booking->practitioner_id;
        return $userBooking || $practitionerBooking;

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
            $this->booking->load('schedule');
            if (Carbon::parse($this->booking->schedule->start_date) < Carbon::now()) {
                $validator->errors()->add('error', 'The schedule cannot be cancelled');
            }
        });
    }

}
