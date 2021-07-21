<?php

namespace App\Http\Requests\Schedule;

use App\Http\Requests\PromotionCode\ValidatePromotionCode;
use App\Http\Requests\Request;
use App\Models\Booking;
use App\Models\ScheduleAvailability;
use App\Models\ScheduleUnavailability;
use App\Models\UserUnavailabilities;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;

class PurchaseScheduleRequest extends Request implements CreateScheduleInterface {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        $idValue = $this->schedule->prices->pluck('id');

        $rules = [
            'price_id' => 'required|exists:prices,id',
            Rule::in($idValue),
            'amount'   => 'required'
        ];

        if ($this->schedule->service->service_type_id === 'appointment') {
            $availabilityRules = [
                'availabilities.*.datetime_from' => 'required_with:availabilities',
                'availabilities'                 => 'required'
            ];

            $rules = array_merge($rules, $availabilityRules);
        }
        return $rules;
    }

    public function withValidator($validator): void {

        $validator->after(function($validator) {
            $schedule = $this->schedule;
            $price = $schedule->prices()->where('id', $this->price_id)->first();

            if (!$price) {
                $validator->errors()->add('price_id', 'Price does not belong to the schedule');
            }

            $bookingsCount = Booking::where('price_id', $this->price_id)->count();

            if ($bookingsCount > 0 && $price->number_available !== null &&
                $bookingsCount >= (int)$price->number_available) {
                $validator->errors()->add('price_id', 'All schedules for that price were sold out');
            }

            if ($this->has('availabilities')) {
                $this->validateAvailabilities($validator);
            }

            if (!empty($this->get('promo_code'))) {
                ValidatePromotionCode::validate($validator, $this->get('promo_code'), $schedule->service, $schedule,
                                                $this->get('amount') * $price->cost);
            }

            if ($schedule->attendees && $schedule->isSoldOut()) {
                $validator->errors()->add('schedule_id', 'All quotes on the schedule are sold out');
            }

        });
    }

    protected function validateAvailabilities($validator): void {
        $availabilitiesRequest = $this->get('availabilities');
        $availabilitiesDatabase = $this->schedule->schedule_availabilities;
        $unavailabilities = $this->schedule->schedule_unavailabilities;
        $globalUnavailabilities = UserUnavailabilities::where('practitioner_id', $this->schedule->service->user_id)->get();

        if (!$availabilitiesDatabase) {
            return;
        }

        foreach ($availabilitiesRequest as $key => $availabilityRequest) {

            if ($globalUnavailabilities && $this->withinUnavailabilities($availabilityRequest['datetime_from'], $globalUnavailabilities)) {
                $validator->errors()
                          ->add("availabilities.$key.datetime_from", 'That date marked as unavailable by practitioner');
            }

            if ($unavailabilities && $this->withinUnavailabilities($availabilityRequest['datetime_from'], $unavailabilities)) {
                $validator->errors()
                          ->add("availabilities.$key.datetime_from", 'That date marked as unavailable by practitioner');
            }

            if (!$this->fits($availabilityRequest['datetime_from'], $availabilitiesDatabase)) {
                $validator->errors()
                          ->add("availabilities.$key.datetime_from", 'No available time slot for selected appointment');
            }
        }
    }

    protected function withinUnavailabilities($datetime, Collection $unavailabilities): bool {

        foreach ($unavailabilities as $unavailability) {

            /** @var ScheduleUnavailability $unavailability */
            /** @var UserUnavailabilities $unavailability */
            if ($unavailability->fits($datetime)) {
                return true;
            }
        }
        return false;
    }

    protected function fits($datetime, $availabilities) {
        foreach ($availabilities as $availability) {
            /** @var ScheduleAvailability $availability */
            if ($availability->fits($datetime)) {
                return true;
            }
        }
        return false;
    }
}
