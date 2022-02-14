<?php

namespace App\Http\Requests\Reschedule;

use App\Http\Requests\Request;
use App\Http\RequestValidators\AvailabilityValidator;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * @property-read Booking $booking
 */
class RescheduleRequestRequest extends Request
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
    public function authorize(): bool
    {
        /** @var User $loggedUser */
        $loggedUser = Auth::user();

        if ($loggedUser->is_admin) {
            return true;
        }

        if ($loggedUser->isPractitioner() && $loggedUser->isFullyRestricted()) {
            return false;
        }

        if ($this->booking) {
            return $this->booking->user_id === $loggedUser->id || $this->booking->practitioner_id === $loggedUser->id;
        }

        if ($this->filled('booking_ids')) {
            $countBookings = count($this->get('booking_ids'));
            $realBookingCnt = Booking::where(static function($query) use ($loggedUser) {
                $query->where('practitioner_id', $loggedUser->id)->orWhere('user_id', $loggedUser->id);
            })->whereIn('id', $this->get('booking_ids'))->whereHas('schedule.service', static function($query) {
                    $query->whereNotIn('service_type_id', config('app.dateless_service_types'));
                })->count();
            return $countBookings === $realBookingCnt;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'new_schedule_id' => 'required|exists:schedules,id',
            'comment'         => 'max:150'
        ];

        if (
            $this->has('new_schedule_id')
            && $this->booking->schedule->service->service_type_id === Service::TYPE_APPOINTMENT
        ) {
            $priceIds = Schedule::find($this->get('new_schedule_id'))->prices()->pluck('id')->toArray();

            $rules = array_merge($rules, [
                'availabilities.*.datetime_from' => 'required_with:availabilities',
                'availabilities'                 => 'required',
                'new_price_id'                       => [
                    'required',
                    Rule::in($priceIds)
                ],
            ]);
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function($validator) {
            if ($this->booking) {
                if (
                    !Service::whereHas('schedules', function($query) {
                        $query->where('schedules.id', $this->booking->schedule_id);
                    })
                    ->whereNotIn('service_type_id', config('app.dateless_service_types'))
                    ->exists()
                ) {
                    $validator->errors()->add('error', 'This type of schedule cannot be rescheduled');
                }

                if (!$this->booking->isActive()) {
                    $validator->errors()->add('error', 'Booking is completed or canceled');
                }

                if (
                    (int) $this->booking->schedule_id === (int) $this->get('new_schedule_id')
                    && $this->booking->schedule->service->service_type_id !== Service::TYPE_APPOINTMENT
                ) {
                    $validator->errors()->add('error', 'Please, select new schedule for reschedule');
                }

                $newSchedule = Schedule::query()
                    ->where('id', $this->get('new_schedule_id'))
                    ->where('is_published', true)
                    ->with('service')
                    ->first();

                if (!$newSchedule) {
                    $validator->errors()->add('new_schedule_id', 'New schedule is not available');
                } elseif (
                    $this->booking->schedule->attendees !== null
                    && $this->booking->schedule->attendees <= Booking::where('schedule_id', $newSchedule->id)->uncanceled()->sum('amount')
                ) {
                    $validator->errors()->add('new_schedule_id', 'There are no free tickets in schedule');
                }

                if ($newSchedule->service->service_type_id === Service::TYPE_APPOINTMENT && $this->has('availabilities')) {
                    $this->availabilityValidator
                        ->setSchedule($newSchedule)
                        ->setDatetimes(array_column($this->get('availabilities'), 'datetime_from'))
                        ->validate($validator);
                }
            }
        });
    }
}
