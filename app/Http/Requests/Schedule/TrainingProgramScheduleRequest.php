<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class TrainingProgramScheduleRequest extends FormRequest
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
            'location_displayed' => 'required|string',
            'refund_terms'       => 'required',
            'prices'             => 'required',
            'prices.*.name'      => 'required',
            'prices.*.cost'      => 'required',
            'prices.*.is_free'   => 'required',
        ];
    }
}
