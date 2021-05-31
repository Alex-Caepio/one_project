<?php

namespace App\Http\Requests\Reschedule;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AcceptRescheduleRequestEmailRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $loggedUser = Auth::user();
        Log::info('LoggedUserID: '.$loggedUser->id);
        Log::info('BookingID: '.$this->reschedule_request->booking->user_id);
        return $this->rescheduleRequest->booking->user_id === $loggedUser->id
               && $loggedUser->currentAccessToken()->can('reschedule_request:accept');
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
            if (!$this->rescheduleRequest->booking->isActive()) {
                $validator->errors()->add('error', 'Booking is completed or canceled');
            }
        });
    }

}
