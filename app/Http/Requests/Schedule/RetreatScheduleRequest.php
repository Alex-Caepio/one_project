<?php

namespace App\Http\Requests\Schedule;

use App\Http\Requests\Request;

class RetreatScheduleRequest extends Request implements CreateScheduleInterface
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
            'start_date'         => 'required|date',
            'end_date'           => 'required|date',
            'venue'              => 'required|string',
            'city'               => 'required|string',
            'country'            => 'required|string',
            'location_displayed' => 'required|string',
            'attendees'          => 'required|integer',
            'refund_terms'       => 'required',
            'prices'             => 'required',
            'prices.*.name'      => 'required',
            'prices.*.cost'      => 'required',
            'prices.*.is_free'   => 'required',


        ];
    }
}
