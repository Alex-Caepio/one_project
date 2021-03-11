<?php

namespace App\Http\Requests\Schedule;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use phpDocumentor\Reflection\Types\This;

class GenericSchedule extends Request implements CreateScheduleInterface {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return (bool)$this->user()->plan;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [];
    }

    public function withValidator($validator): void {
        $validator->after(function($validator) {
            $plan = $this->user()->plan;
            $totalSchedules = $this->service->schedules()->count();
            if (!$plan->unlimited_bookings && $this->attendees > $plan->amount_bookings) {
                $validator->errors()->add('attendees', "You're limited to {$plan->amount_bookings} attendees");
            }

            //if user not allowed to list paid services
            if (!$plan->list_paid_services && $this->has('prices') && $this->hasPaidPrices()) {
                $validator->errors()->add('prices.*.cost', 'Your plan restricts you from publishing paid services.');
            }

            //if user not allowed to list free services
            if (!$plan->list_free_services && $this->has('prices') && $this->hasFreePrices()) {
                $validator->errors()->add('prices.*.cost', 'Your plan restricts you from publishing free services.');
            }

            if (!$plan->schedules_per_service_unlimited && $totalSchedules >= $plan->schedules_per_service) {
                $validator->errors()->add('service_id', 'The schedules limit on the service has been exceeded.');
            }
        });
    }

    protected function hasPaidPrices() {
        return !collect($this->prices)->filter(function($item) {
            return isset($item['cost']) && $item['cost'] > 0;
        })->isEmpty();
    }

    protected function hasFreePrices() {
        return !collect($this->prices)->filter(function($item) {
            return isset($item['cost']) && in_array($item['cost'], [0, null]);
        })->isEmpty();
    }
}
