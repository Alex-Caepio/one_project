<?php


namespace App\Traits;


use App\Helpers\UserRightsHelper;
use App\Models\Schedule;
use App\Models\Service;

trait ScheduleValidator {

    public function userScheduleValidator($validator, Service $service) {

        $schedulesQuery = $service->schedules()->where('title', $this->title);
        if (isset($this->schedule) && $this->schedule instanceof Schedule) {
            $schedulesQuery->where('id', '!=', $this->schedule->id);
        }

        if ($schedulesQuery->exists()) {
            $validator->errors()->add('title', 'The schedule name is not unique!');
        }

        $plan = $this->user()->plan;

        if (!UserRightsHelper::userAllowAddSchedule($this->user(), $service)) {
            $validator->errors()->add('service_id', 'The schedules limit on the service has been exceeded.');
        }

        if (!UserRightsHelper::userAllowAttendees($this->user(), $this->attendees)) {
            $validator->errors()->add('attendees', "You're limited to {$plan->amount_bookings} attendees");
        }

        if ($this->has('prices')) {

            //if user not allowed to add prices
            if (!UserRightsHelper::userAllowAddPriceOptions($this->user(), count($this->prices))) {
                $validator->errors()->add('prices.*.name', "You`re restricted to {$plan->pricing_options_per_service} prices.");
            }

            //if user not allowed to list paid services
            if ($this->hasPaidPrices() && !UserRightsHelper::userAllowPaidSchedule($this->user())) {
                $validator->errors()->add('prices.*.cost', 'Your plan restricts you from publishing paid services.');
            }

            //if user not allowed to list free services
            if ($this->hasFreePrices() && !UserRightsHelper::userAllowFreeSchedule($this->user())) {
                $validator->errors()->add('prices.*.cost', 'Your plan restricts you from publishing free services.');
            }
        }
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

    public function prepareForValidation(): void {
        if ($this->prices) {
            foreach ($this->prices as $key => $value) {
                $another[$key] = $value;

                if ($another[$key]['is_free']) {
                    $another[$key]['cost'] = 0;
                }

                if (!empty($another[$key]['duration'])) {
                    list($h, $m) = explode(':', $another[$key]['duration']);
                    $another[$key]['duration'] = ($h * 60) + $m;
                }
            }
            $this->merge(['prices' => $another]);
        }
    }

}
