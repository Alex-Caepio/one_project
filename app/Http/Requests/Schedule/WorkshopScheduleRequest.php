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
            'title' => 'required|string|min:5',
            'promo_code' => 'string|min:5',
            'service_id' => 'integer',
            'location_id' => 'integer',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:today',
            'attendees' => 'nullable|required_if:appointment,physical|integer',
            'cost' => 'integer',
            'comments' => 'nullable|string',
            'venue_address' => 'required_if:appointment,physical|max:255',
            'city' => 'nullable|required_if:appointment,physical|string',
            'country' => 'nullable|required_if:appointment,physical|string',
            'location_displayed' => 'string',
            'post_code' => 'required_if:appointment,physical',
            'refund_terms' => 'required',
            'prices' => 'required|array',
            'prices.*.name' => 'required',
            'prices.*.cost' => 'required_if:prices.*.is_free,false',
            'prices.*.is_free' => 'required',
            'prices.*.available_till' => 'before:end_date',

            'deposit_amount' => 'required_if:deposit_accepted,true',
            'deposit_final_date' => 'required_if:deposit_accepted,true',

            'url' => 'nullable|required_if:appointment,virtual|string',
        ];
    }

    public function messages()
    {
        return [
            'prices.*.name.required'           => 'The name field is required when setting prices.',
            'prices.*.cost.required_if'           => 'The cost field is required when setting prices.',
            'prices.*.is_free.required'        => 'The is_free field is required when setting prices.',
            'prices.*.available_till.before'   => 'The available_till field should be before the end_date field.',
        ];
    }
}
