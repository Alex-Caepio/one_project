<?php

namespace App\Http\Requests\Schedule;

class ClassScheduleRequest extends GenericSchedule
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
            'title'              => 'required|string|min:5',
            'is_virtual'         => 'required|boolean',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date',
            'venue'              => 'required|string',
            'city'               => 'required|string',
            'country'            => 'required|string',
            'location_displayed' => 'required|string',
            'attendees'          => 'required|integer',
            'url'                => 'required|string',
            'refund_terms'       => 'required',
            'prices'             => 'required',
            'prices.*.name'      => 'required',
            'prices.*.cost'      => 'required',
            'prices.*.is_free'   => 'required',
            'repeat_every'       => 'required_if:repeat,monthly',
            'repeat_period'      => 'required_if:repeat,monthly',
        ];
    }
}
