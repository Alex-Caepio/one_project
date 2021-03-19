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
            'start_date'         => 'required|date|after:today',
            'end_date'           => 'required|date|after:today',
            'venue_address'      => 'required_if:appointment,physical|max:255',
            'city'               => 'required_if:appointment,physical|required|string',
            'country'            => 'required_if:appointment,physical|string',
            'location_displayed' => 'required|string',
            'attendees'          => 'required|integer',
            'url'                => 'nullable|required_if:appointment,virtual|string',
            'refund_terms'       => 'required',
            'prices'             => 'required|array',
            'prices.*.name'      => 'required',
            'prices.*.cost'      => 'required',
            'prices.*.is_free'   => 'required',
            'prices.*.available_till' => 'before:end_date',

            'repeat_every'       => 'required_if:repeat,monthly',
            'repeat_period'      => 'required_if:repeat,monthly',

            'deposit_amount'     => 'required_if:deposit_accepted,true',
            'deposit_final_date' => 'required_if:deposit_accepted,true',
        ];
    }

    public function messages()
    {
        return [
            'prices.*.name.required'           => 'The name field is required when setting prices.',
            'prices.*.cost.required'           => 'The cost field is required when setting prices.',
            'prices.*.is_free.required'        => 'The is_free field is required when setting prices.',
            'prices.*.available_till.before'   => 'The available_till field should be before the end_date field.',
        ];
    }
}
