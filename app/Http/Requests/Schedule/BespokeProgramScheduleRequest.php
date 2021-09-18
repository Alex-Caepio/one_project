<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Validation\Rule;

class BespokeProgramScheduleRequest extends GenericSchedule
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'                        => 'required|string|min:5',
            'location_displayed'           => 'required|string',
            'prices'                       => 'required|array',
            'prices.*.name'                => Rule::requiredIf(
                function () {
                    return count($this->prices) > 1;
                }
            ),
            'prices.*.cost'                => 'required_if:prices.*.is_free,false',
            'prices.*.is_free'             => 'required',
            'deposit_amount'               => 'nullable|required_if:deposit_accepted,true',
            'deposit_instalment_frequency' => 'nullable|required_if:deposit_accepted,true|min:1|integer|max:31',
            'deposit_instalments'          => 'nullable|required_if:deposit_accepted,true|min:1|integer|max:100',
            'refund_terms'                 => 'nullable',
            'comments'                     => 'nullable|string|max:1000',
            'booking_message'              => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'prices.*.name.required'         => 'The name field is required when setting prices.',
            'prices.*.cost.required_if'      => 'The cost field is required when setting prices.',
            'prices.*.is_free.required'      => 'The is_free field is required when setting prices.',
            'prices.*.available_till.before' => 'The available_till field should be before the end_date field.',
        ];
    }
}
