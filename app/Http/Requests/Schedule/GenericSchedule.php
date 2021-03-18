<?php

namespace App\Http\Requests\Schedule;

use App\Http\Requests\Request;

class GenericSchedule extends Request implements CreateScheduleInterface
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return (bool)$this->user()->plan;
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $plan           = $this->user()->plan;
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

            foreach ($this->service->schedules as $schedule)
            {
                if($schedule->title == $this->title) {
                    $validator->errors()->add('title', 'The schedule name is not unique!');
                }
            }
        });
    }

    protected function hasPaidPrices()
    {
        return !collect($this->prices)->filter(function ($item) {
            return isset($item['cost']) && $item['cost'] > 0;
        })->isEmpty();
    }

    protected function hasFreePrices()
    {
        return !collect($this->prices)->filter(function ($item) {
            return isset($item['cost']) && in_array($item['cost'], [0, null]);
        })->isEmpty();
    }

    public function prepareForValidation()
    {
        $plan = $this->user()->plan;

        if ($plan->list_paid_services && $this->user()->isPractitioner()) {
            $another = [];
            foreach ($this->prices as $key => $value) {
                $another[$key] = $value;
                $another[$key]['cost'] = 0;
            }
            $this->merge(['prices' => $another]);
        }

        if($this->prices){
            foreach ($this->prices as $key => $value) {
                $another[$key] = $value;
                if(!empty($another[$key]['duration'])){
                    list($h, $m) = explode(':', $another[$key]['duration']);
                    $another[$key]['duration'] = ($h * 60) + $m;
                }
            }
            $this->merge(['prices' => $another]);
        }
    }
}
