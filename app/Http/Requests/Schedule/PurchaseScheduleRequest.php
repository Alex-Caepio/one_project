<?php

namespace App\Http\Requests\Schedule;

use App\Http\Requests\PromotionCode\ValidatePromotionCode;
use App\Http\Requests\Request;
use App\Http\RequestValidators\AvailabilityValidator;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Service;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * @property-read Schedule $schedule
 */
class PurchaseScheduleRequest extends Request implements CreateScheduleInterface
{
    private AvailabilityValidator $availabilityValidator;

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        $this->availabilityValidator = app(AvailabilityValidator::class);
    }

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
        $idValue = $this->schedule->prices->pluck('id');

        $rules = [
            'price_id' => 'required|exists:prices,id',
            Rule::in($idValue),
            'amount' => 'required',
            'installments' => 'nullable|integer|min:1',
            'authorize' => Rule::requiredIf(function () {
                return $this->route()->getName() === 'purchase-process' && isset($this->installments) &&
                       (int)$this->installments > 1;
            })
        ];

        if ($this->schedule->service->service_type_id === Service::TYPE_APPOINTMENT) {
            $availabilityRules = [
                'availabilities.*.datetime_from' => 'required_with:availabilities',
                'availabilities'                 => 'required'
            ];

            $rules = array_merge($rules, $availabilityRules);
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $schedule = $this->schedule;
            $price = $schedule->prices()->where('id', $this->price_id)->first();

            if (!$price) {
                $validator->errors()->add('price_id', 'Price does not belong to the schedule');
                return;
            }

            $bookingsCount = Booking::where('price_id', $this->price_id)->uncanceled()->count();
            $requiredAmount = (int)$this->request->get('amount');

            if (
                $schedule->service->service_type_id !== Service::TYPE_APPOINTMENT
                && (int) $price->number_available > 0
                && ($bookingsCount + $requiredAmount) > (int) $price->number_available
            ) {
                $validator->errors()->add('price_id', 'All schedules for that price were sold out');
                return;
            }

            if ($schedule->service->service_type_id === Service::TYPE_APPOINTMENT && $this->has('availabilities')) {
                $this->availabilityValidator
                    ->setSchedule($this->schedule)
                    ->setDatetimes(array_column($this->get('availabilities'), 'datetime_from'))
                    ->validate($validator);
            }

            if (!empty($this->get('promo_code'))) {
                ValidatePromotionCode::validate(
                    $validator,
                    $this->get('promo_code'),
                    $schedule->service,
                    $schedule,
                    $this->get('amount') * $price->cost
                );
            }

            if ($schedule->attendees) {
                $availableTicketsPerSchedule = $schedule->getAvailableTicketsCount();
                if ($availableTicketsPerSchedule < $requiredAmount) {
                    $validator->errors()->add('schedule_id', 'All quotes on the schedule are sold out');
                }
            }
        });
    }
}
