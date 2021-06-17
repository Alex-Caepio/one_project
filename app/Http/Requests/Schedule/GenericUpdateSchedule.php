<?php

namespace App\Http\Requests\Schedule;

use App\Http\Requests\Request;
use App\Traits\ScheduleValidator;

class GenericUpdateSchedule extends Request implements CreateScheduleInterface {
    use ScheduleValidator;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return $this->user()->is_admin ||
               ($this->user()->onlyUnpublishedAllowed() && $this->schedule->service->user_id === $this->user()->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        if ($this->schedule->service->service_type_id === 'appointment') {
            return [
                'schedule_unavailabilities.*.start_date' => 'required_with:schedule_unavailabilities',
                'schedule_unavailabilities.*.end_date'   => 'required_with:schedule_unavailabilities',
                'refund_terms'                           => 'required',
            ];
        }

        if ($this->schedule->service->service_type_id !== 'bespoke') {
            return [
                'refund_terms' => 'required',
            ];
        }

        return [];
    }

    public function withValidator($validator): void {
        $validator->after(function($validator) {
            $this->userScheduleValidator($validator, $this->schedule->service);
        });
    }

    public function messages() {
        return [
            'schedule_unavailabilities.*.start_date.required_with' => 'The start date field is required when setting unavailabilities.',
            'schedule_unavailabilities.*.end_date.required_with'   => 'The end date field is required when setting unavailabilities.',
        ];
    }
}
