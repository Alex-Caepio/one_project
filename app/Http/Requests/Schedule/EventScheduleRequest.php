<?php

namespace App\Http\Requests\Schedule;

class EventScheduleRequest extends GenericSchedule
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
        if($this->is_published == false) {
            return [
                'title' => 'string|min:5',
                'start_date' => 'date|after:today',
                'end_date' => 'date|after:today',
                'venue_address' => 'max:255',
                'city' => 'string',
                'country' => 'string',
                'location_displayed' => 'string',
                'attendees' => 'integer',
                'url' => 'nullable|string',
                'prices' => 'array',
                'prices.*.available_till' => 'before:end_date',
            ];
        } else {
            return [
                'title' => 'required|string|min:5',
                'start_date' => 'required|date|after:today',
                'end_date' => 'required|date|after:today',
                'venue_address' => 'required_if:appointment,physical|max:255',
                'city' => 'required_if:appointment,physical|string',
                'country' => 'required_if:appointment,physical|string',
                'post_code' => 'required_if:appointment,physical',
                'location_displayed' => 'required|string',
                'attendees' => 'required|integer',
                'url' => 'nullable|required_if:appointment,virtual|string',
                'refund_terms' => 'required',
                'prices' => 'required|array',
                'prices.*.name' => 'required',
                'prices.*.cost' => 'required_if:prices.*.is_free,false',
                'prices.*.is_free' => 'required',
                'prices.*.available_till' => 'before:end_date',

                'deposit_amount' => 'required_if:deposit_accepted,true',
                'deposit_final_date' => 'required_if:deposit_accepted,true',
            ];
        }
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
