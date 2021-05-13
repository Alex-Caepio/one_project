<?php

namespace App\Http\Requests\Cancellation;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
        return $userBooking || $practitionerBooking || Auth::user()->is_admin;

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'role' => [
                'nullable',
                Rule::in([User::ACCOUNT_PRACTITIONER, User::ACCOUNT_CLIENT]),
            ],
        ];
    }

    public function withValidator($validator): void {
        $validator->after(function($validator) {
            if (!$this->booking->isActive()) {
                $validator->errors()->add('error', 'Booking is completed or canceled');
            }
        });
    }

}
