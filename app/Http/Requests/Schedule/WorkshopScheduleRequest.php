<?php

namespace App\Http\Requests\Schedule;

class WorkshopScheduleRequest extends GenericSchedule
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
            'promo_code'         => 'string|min:5',
            'service_id'         => 'integer',
            'location_id'        => 'integer',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date',
            'attendees'          => 'required|integer',
            'cost'               => 'integer',
            'comments'           => 'nullable|string',
            'venue_address'      => 'required|max:255',
            'city'               => 'required_if:appointment,physical|required|string',
            'country'            => 'required_if:appointment,physical|string',
            'location_displayed' => 'required|string',
            'post_code'          => 'required_if:appointment,physical|required',
            'refund_terms'       => 'required',
            'prices'             => 'required|array',
            'prices.*.name'      => 'required',
            'prices.*.cost'      => 'required',
            'prices.*.is_free'   => 'required',

        ];
    }
}
