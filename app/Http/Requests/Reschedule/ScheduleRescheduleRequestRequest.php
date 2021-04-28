<?php

namespace App\Http\Requests\Reschedule;

use App\Http\Requests\Request;
use App\Models\Booking;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ScheduleRescheduleRequestRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return Auth::user()->is_admin || $this->schedule->service->user_id === Auth::id();
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
            if ($this->schedule->start_date && Carbon::parse($this->schedule->start_date) < Carbon::now()) {
                $validator->errors()->add('error', 'The schedule cannot be cancelled');
            }
        });
    }
}
