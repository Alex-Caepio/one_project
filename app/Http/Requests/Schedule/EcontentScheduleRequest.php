<?php

namespace App\Http\Requests\Schedule;

class EcontentScheduleRequest extends GenericSchedule
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
                'promo_code' => 'string|min:5',
                'service_id' => 'integer',
                'location_id' => 'integer',
                'start_date' => 'date|after:today',
                'end_date' => 'date|after:today',
                'attendees' => 'integer',
                'cost' => 'integer',
                'comments' => 'nullable|string',
                'venue_address' => 'max:255',
                'city' => 'nullable|string',
                'country' => 'nullable|string',
                'location_displayed' => 'string',
                'prices' => 'array',
                'prices.*.available_till' => 'before:end_date',
            ];
        } else {
            return [
                'title' => 'required|string|min:5',
                'promo_code' => 'string|min:5',
                'service_id' => 'integer',
                'location_id' => 'integer',
                'start_date' => 'date|after:today',
                'end_date' => 'date|after:today',
                'attendees' => 'integer',
                'cost' => 'integer',
                'comments' => 'nullable|string',
                'venue_address' => 'required_if:appointment,physical|max:255',
                'city' => 'nullable|string',
                'country' => 'nullable|string',
                'location_displayed' => 'string',
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
