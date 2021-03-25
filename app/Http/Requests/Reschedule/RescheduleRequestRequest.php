<?php

namespace App\Http\Requests\Reschedule;

use App\Models\Booking;
use App\Models\Schedule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RescheduleRequestRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $loggedUser = Auth::user();
        if ($this->booking) {
            return ($loggedUser->isPractitioner() && $this->booking->practitioner_id === $loggedUser->id) ||
                   ($loggedUser->isClient() && $this->booking->user_id === $loggedUser->id);
        }

        if ($this->filled('booking_ids') && $loggedUser->isPractitioner()) {
            return count($this->get('bookings_id')) === Booking::where('practitioner_id', $loggedUser->id)
                                                               ->whereIn('id', $this->get('bookings_id'))->count();
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'new_schedule_id' => 'required|exists:schedules,id',
            'comment'         => 'max:150'
        ];
    }

    public function withValidator($validator): void {
        $validator->after(function($validator) {
            if ($this->booking && $this->booking->schedule->service->id !==
                    Schedule::find($this->get('new_schedule_id'))->service->id) {
                    $validator->errors()->add('new_schedule_id', 'This schedule does not belong to the service.');
                }
        });
    }
}
