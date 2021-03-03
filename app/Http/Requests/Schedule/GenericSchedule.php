<?php

namespace App\Http\Requests\Schedule;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use phpDocumentor\Reflection\Types\This;

class GenericSchedule extends Request implements CreateScheduleInterface
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return (bool) $this->user()->plan;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

   public function withValidator($validator): void {
       $validator->after(function($validator) {
           $plan = $this->user()->plan;

           if (!$plan->unlimited_bookings) {
                $this->validate(['attendees' => "max:{$this->user()->plan->amount_bookings}"]);
           }

            if(!$plan->list_paid_services) {
                $this->validate(['prices.*.cost' => Rule::in([null, 0])]);
            }

       });
   }
}
