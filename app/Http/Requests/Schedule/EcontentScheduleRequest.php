<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Validation\Rule;

class EcontentScheduleRequest extends GenericSchedule
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'                   => 'required|string|min:5',
            'promo_code'              => 'string|min:5',
            'service_id'              => 'integer',
            'location_id'             => 'integer',
            'start_date'              => 'date|after:today',
            'end_date'                => 'date|after:start_date',
            'attendees'               => 'required|integer',
            'cost'                    => 'integer',
            'comments'                => 'nullable|string|max:1000',
            'booking_message'         => 'nullable|string|max:1000',
            'venue_address'           => 'required_if:appointment,physical|max:255',
            'city'                    => 'nullable|string',
            'country_id'              => 'nullable|exists:countries,id',
            'location_displayed'      => 'required|string',
            'refund_terms'            => 'required',
            'prices'                  => 'required|array',
            'prices.*.name'           => Rule::requiredIf(
                function () {
                    return count($this->prices) > 1;
                }
            ),
            'prices.*.cost'           => 'required_if:prices.*.is_free,false',
            'prices.*.is_free'        => 'required',
            'prices.*.available_till' => 'nullable|before:end_date',
            'deposit_amount'          => 'required_if:deposit_accepted,true',
            'deposit_final_date'      => 'required_if:deposit_accepted,true',
        ];
    }

    public function messages()
    {
        return [
            'prices.*.name.required'         => 'The name field is required when setting prices.',
            'prices.*.cost.required_if'      => 'The cost field is required when setting prices.',
            'prices.*.is_free.required'      => 'The is_free field is required when setting prices.',
            'prices.*.available_till.before' => 'The available_till field should be before the end_date field.',
            'end_date.after'                 => 'End date and time must be after the start date and time.',
        ];
    }
}
