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
        return true;
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
           $unlimitedBookings = $this->user()->plan->unlimited_bookings;
           $listPaidServices = $this->user()->plan->list_paid_services;

           if ($unlimitedBookings == 0) {
                $this->validate(['attendees' => "max:{$this->user()->plan->amount_bookings}"]);
           }

            if($this->user()->account_type == 'practitioner' && $listPaidServices == 0) {
                $this->validate(['prices.*.cost' => Rule::in(['null', 0])]);
            }

       });
   }
}
