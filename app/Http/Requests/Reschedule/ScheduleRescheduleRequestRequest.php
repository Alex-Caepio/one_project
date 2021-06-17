<?php

namespace App\Http\Requests\Reschedule;

use App\Http\Requests\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ScheduleRescheduleRequestRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return Auth::user()->is_admin || (!Auth::user()->isFullyRestricted()
                                          && $this->schedule->service
                                          && $this->schedule->service->user_id === Auth::id());
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
            if ((int)$this->schedule->id === (int)$this->get('new_schedule_id')) {
                $validator->errors()->add('error', 'Please, select new schedule for reschedule');
            }

            if ($this->schedule->start_date && Carbon::parse($this->schedule->start_date) < Carbon::now()) {
                $validator->errors()->add('error', 'The schedule cannot be cancelled');
            }
        });
    }
}
