<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentScheduleRequest extends FormRequest
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
            'prices'                        => 'required',
            'prices.*.name'                 => 'required',
            'prices.*.duration'             => 'required',
            'prices.*.cost'                 => 'required',
            'prices.*.is_free'              => 'required',
            'notice_min_time'               => 'required',
            'notice_min_period'             => 'required',
            'buffer_time'                   => 'required',
            'buffer_period'                 => 'required',

            'availabilities.*.days'         => 'required_with:availabilities',
            'availabilities.*.start_time'   => 'required_with:availabilities',
            'availabilities.*.end_time'     => 'required_with:availabilities',

            'unavailabilities.*.start_date' => 'required_with:unavailabilities',
            'unavailabilities.*.end_date'   => 'required_with:unavailabilities',

            'availabilities'                => 'required',
        ];
    }
}
