<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class ClassScheduleRequest extends FormRequest
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
            'is_virtual'         => 'required|boolean',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date',
            'venue'              => 'required|string',
            'city'               => 'required|string',
            'country'            => 'required|string',
            'location_displayed' => 'required|string',
            'attendees'          => 'required|integer',
            'url'                => 'required|string',
            'refund_terms'       => 'required',
            'repeat'             => 'required',
            'prices'             => 'required',
            'prices.*.name'      => 'required',
            'prices.*.cost'      => 'required',
            'prices.*.is_free'   => 'required',
            'repeat_every'       => 'required_if:repeat,monthly',
            'repeat_period'      => 'required_if:repeat,monthly',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $schedule = $this->schedule;
            if (!$schedule->prices()) {
                $validator->errors()->add('price', "You have not filled in the field \"Schedule price\"");
            }
            if (!$schedule->prices()->name) {
                $validator->errors()->add('name', "You have not filled in the field \"Name\"");
            }
            if (!$schedule->prices()->cost) {
                $validator->errors()->add('cost', "You have not filled in the field \"Cost\"");
            }
            if (!$schedule->prices()->is_free) {
                $validator->errors()->add('is_free', "You have not filled in the field \"Is free\"");
            }
            if($schedule->repeat === 'monthly') {
                $validator->addRules([
                    'repeat_every'  => 'required',
                    'repeat_period' => 'required'
                ]);
            }
        });
    }
}
