<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Validation\Rule;

class RetreatScheduleRequest extends GenericSchedule
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'                     => 'required|string|min:5',
            'start_date'                => 'required|date|after:today',
            'end_date'                  => 'required|date|after:start_date',
            'venue_address'             => 'required_if:appointment,physical|max:255',
            'city'                      => 'required_if:appointment,physical|required|string',
            'country_id'                => 'required_if:appointment,physical|exists:countries,id',
            'location_displayed'        => 'required|string',
            'attendees'                 => 'required|integer|min:1',
            'refund_terms'              => 'required',
            'prices'                    => 'required|array',
            'prices.*.name'             => Rule::requiredIf(
                function () {
                    return count($this->prices) > 1;
                }
            ),
            'prices.*.cost'             => 'required_if:prices.*.is_free,false',
            'prices.*.is_free'          => 'required',
            'prices.*.available_till'   => 'nullable|before:end_date',
            'prices.*.number_available' => 'nullable|integer|lte:attendees',
            'deposit_amount'            => 'required_if:deposit_accepted,true',
            'deposit_final_date'        => 'required_if:deposit_accepted,true',
            'comments'                  => 'nullable|string|max:1000',
            'booking_message'           => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'prices.*.name.required'                  => 'The name field is required when setting prices.',
            'prices.*.cost.required_if'               => 'The cost field is required when setting prices.',
            'prices.*.is_free.required'               => 'The is_free field is required when setting prices.',
            'prices.*.available_till.before'          => 'The available_till field should be before the end_date field.',
            'prices.*.number_available.lte:attendees' => 'The tickets_available field should be less or equal max attendees.',
            'end_date.after'                          => 'End date and time must be after the start date and time.',
        ];
    }
}
