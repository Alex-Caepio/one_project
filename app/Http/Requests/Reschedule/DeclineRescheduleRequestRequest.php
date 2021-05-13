<?php

namespace App\Http\Requests\Reschedule;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DeclineRescheduleRequestRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $loggedUser = Auth::user();
        return $this->rescheduleRequest->booking->user_id === $loggedUser->id ||
               $this->rescheduleRequest->booking->practitioner_id === $loggedUser->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [];
    }

    public function withValidator($validator): void {
        $validator->after(function($validator) {
            if (!$this->booking->isActive()) {
                $validator->errors()->add('error', 'Booking is completed or canceled');
            }
        });
    }


}
