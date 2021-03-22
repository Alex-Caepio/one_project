<?php

namespace App\Http\Requests\Schedule;

use App\Http\Requests\PromotionCode\ValidatePromotionCode;
use App\Http\Requests\Request;
use App\Models\ScheduleAvailability;
use App\Models\Booking;
use App\Models\Price;
use App\Models\ScheduleUnavailability;
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
            'price_id' => 'required_if:is_free,false|exists:prices,id',
            Rule::in($idValue),
            'amount'   => 'required'
        ];

        if ($this->schedule->service->service_type_id === 'appointment') {
            $availabilityRules = [
                'availabilities.*.availability_id' => 'required_with:availabilities',
                'availabilities.*.datetime_from'   => 'required_with:availabilities',
            ];

            $rules = array_merge($rules, $availabilityRules);
        }
        return $rules;
    }

   public function withValidator($validator): void {

       $validator->after(function($validator) {
           $schedule = $this->schedule;
           $priceId = $this->price_id;

           $bookingsCount = Booking::where('price_id', $this->price_id)->count();
           $price = Price::find($this->price_id);

           if ($this->has('availabilities')) {
               $this->validateAvailabilities($validator);
           }

           if (!empty($this->get('promo_code'))) {
               ValidatePromotionCode::validate($validator, $this->get('promo_code'), $schedule->service, $schedule);
           }

           if ($schedule->attendees && $schedule->isSoldOut()) {
               $validator->errors()->add('schedule_id', 'All quotes on the schedule are sold out');
           }

           if (!$schedule->prices()->where('id', $priceId)->exists()) {
               $validator->errors()->add('price_id', 'Price does not belong to the schedule');
           }

           if($bookingsCount >= $price->number_available){
               $validator->errors()->add('price_id', 'All schedules for that price were sold out');
           }
       });
   }

    protected function validateAvailabilities($validator): void {
        $availabilitiesRequest = $this->get('availabilities');
        $availabilitiesDatabase = $this->schedule->schedule_availabilities;
        $unavailabilities = $this->schedule->schedule_unavailabilities;

        if (!$availabilitiesDatabase) {
            return;
        }

        foreach ($availabilitiesRequest as $key => $availabilityRequest) {

            if ($this->withinUnavailabilities($availabilityRequest['datetime_from'], $unavailabilities)) {
                $validator->errors()
                          ->add("availabilities.$key.datetime_from", 'That date marked as unavailable by practitioner');
            }

            if (!$this->fits($availabilityRequest['datetime_from'], $availabilitiesDatabase)) {
                $validator->errors()
                          ->add("availabilities.$key.datetime_from", 'No available time slot for selected appointment');
            }
        }
    }

    protected function withinUnavailabilities($datetime, $unavailabilities): bool {
        if (!$unavailabilities) {
            return false;
        }

        foreach ($unavailabilities as $unavailability) {

            /** @var ScheduleUnavailability $unavailability */
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
