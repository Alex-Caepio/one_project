<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Validation\Rule;

class EventScheduleRequest extends GenericSchedule
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
            'end_date'                  => 'required|date|after:today',
            'venue_address'             => 'nullable|required_if:appointment,physical|max:255',
            'city'                      => 'nullable|required_if:appointment,physical|string',
            'country_id'                => 'nullable|required_if:appointment,physical|exists:countries,id',
            'post_code'                 => 'nullable|required_if:appointment,physical',
            'location_displayed'        => 'required|string',
            'attendees'                 => 'required_if:appointment,physical|integer',
            'url'                       => 'nullable|required_if:appointment,virtual|string',
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
        ];
    }
}
