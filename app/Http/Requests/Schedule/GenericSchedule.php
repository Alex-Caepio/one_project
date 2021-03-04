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
           $totalSchedules = $this->service->schedules()->count();

           if (!$plan->unlimited_bookings) {
                $this->validate(['attendees' => "max:{$this->user()->plan->amount_bookings}"]);
           }

//            if(!$plan->list_paid_services) {
//                $validator->errors()->add('prices.*.cost', 'Your plan restricts you from publishing paid services.');
//            }
//
//            if(!$plan->list_free_services) {
//                $this->validate(['prices.*.cost' => 'min:0|not_in:0']);
//                $validator->errors()->add('prices.*.cost', 'Your plan restricts you from publishing free services.');
//            }

            if(!$plan->schedules_per_service_unlimited && $totalSchedules >= $plan->schedules_per_service){
                $validator->errors()->add('service_id', 'The schedules limit on the service has been exceeded.');
            }
       });
   }
}
