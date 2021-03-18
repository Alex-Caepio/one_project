<?php

namespace App\Http\Requests\Schedule;

class AppointmentScheduleRequest extends GenericSchedule
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'prices'                        => 'required|array',
            'prices.*.name'                 => 'required',
            'prices.*.duration'             => 'required',
            'prices.*.cost'                 => 'required',
            'prices.*.is_free'              => 'required',
            'prices.*.available_till'       => 'before:end_date',

            'notice_min_time'               => 'required',
            'notice_min_period'             => 'required',
            'buffer_time'                   => 'required',

            'schedule_availabilities.*.days'         => 'required_with:schedule_availabilities',
            'schedule_availabilities.*.start_time'   => 'required_with:schedule_availabilities',
            'schedule_availabilities.*.end_time'     => 'required_with:schedule_availabilities',

            'schedule_unavailabilities.*.start_date' => 'required_with:schedule_unavailabilities',
            'schedule_unavailabilities.*.end_date'   => 'required_with:schedule_unavailabilities',

            'schedule_availabilities'                => 'required|array',

            'deposit_amount'                => 'required_if:deposit_accepted,true',
            'deposit_final_date'            => 'required_if:deposit_accepted,true',
        ];
    }

    public function messages()
    {
        return [
            'schedule_unavailabilities.*.start_date.required_with' => 'The start date field is required when setting unavailabilities.',
            'schedule_unavailabilities.*.end_date.required_with'   => 'The end date field is required when setting unavailabilities.',
            'schedule_availabilities.*.days.required_with'         => 'The days field is required when setting availabilities.',
            'schedule_availabilities.*.start_time.required_with'   => 'The start time field is required when setting availabilities.',
            'schedule_availabilities.*.end_time.required_with'     => 'The end time field is required when setting availabilities.',
        ];
    }
}
