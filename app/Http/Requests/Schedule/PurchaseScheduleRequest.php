<?php

namespace App\Http\Requests\Schedule;

use App\Models\ScheduleFreeze;
use App\Models\ScheduleUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PurchaseScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'=>'string|min:5',
            'promo_code'=>'string|min:5',
            'service_id'=>'integer',
            'location_id'=>'integer',
            'start_date'=>'date',
            'end_date'=>'date',
            'attendees'=>'integer',
            'cost'=>'integer',
            'comments'=>'string',
            'venue'=>'string',
            'city'=>'string',
            'country'=>'string',
            'location_displayed'=>'string',

        ];
    }
}
