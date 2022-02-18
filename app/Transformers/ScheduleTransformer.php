<?php

namespace App\Transformers;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\ScheduleSnapshot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ScheduleTransformer extends Transformer
{
    private ?User $authUser;
    private ?Collection $bookings;

    public function __construct()
    {
        $this->authUser = Auth::user() === null ? Auth::guard('sanctum')->user() : null;
        $this->bookings = null;
    }

    public function setBooking(Booking $booking) {
        $this->bookings = collect([$booking]);
        return $this;
    }

    protected $availableIncludes = [
        'location',
        'prices',
        'service',
        'users',
        'media_files',
        'schedule_availabilities',
        'schedule_unavailabilities',
        'schedule_files',
        'schedule_hidden_files',
        'bookings',
        'reschedule_requests',
        'country'
    ];

    public function transform(Schedule $schedule)
    {
        if (isset($this->bookings)) {
            $schedule = $this->bookings->first()->snapshot->schedule;
        }

        return [
            'id'                           => $schedule instanceof ScheduleSnapshot ? $schedule->schedule->id : $schedule->id,
            'title'                        => $schedule->title,
            'location_id'                  => $schedule->location_id,
            'service_id'                   => $schedule instanceof ScheduleSnapshot ? $schedule->schedule->service_id :
                                                ($schedule->service_id ?? $schedule->service->id),
            'start_date'                   => $schedule->start_date ? Carbon::parse($schedule->start_date) : null,
            'end_date'                     => $schedule->end_date ? Carbon::parse($schedule->end_date) : null,
            'attendees'                    => $schedule->attendees,
            'attendees_available'          => $schedule->getAvailableTicketsCount(),
            'cost'                         => $schedule->cost,
            'comments'                     => $schedule->comments,
            'city'                         => $schedule->city,
            'post_code'                    => $schedule->post_code,
            'country_id'                   => $schedule->country_id,
            'location_displayed'           => $schedule->location_displayed,
            'meals_breakfast'              => (bool)$schedule->meals_breakfast,
            'meals_lunch'                  => (bool)$schedule->meals_lunch,
            'meals_dinner'                 => (bool)$schedule->meals_dinner,
            'meals_alcoholic_beverages'    => (bool)$schedule->meals_alcoholic_beverages,
            'meals_dietry_accomodated'     => (bool)$schedule->meals_dietry_accomodated,
            'refund_terms'                 => $schedule->refund_terms,
            'deposit_accepted'             => (bool)$schedule->deposit_accepted,
            'deposit_amount'               => $schedule->deposit_amount,
            'deposit_instalments'          => $schedule->deposit_instalments,
            'deposit_instalment_frequency' => $schedule->deposit_instalment_frequency,
            'deposit_final_date'           => $schedule->deposit_final_date
                ? Carbon::parse($schedule->deposit_final_date)->setTime(0, 0)
                : null,
            'booking_message'              => $schedule->booking_message,
            'url'                          => $schedule->url,
            'book_full_series'             => $schedule->book_full_series,
            'accomodation'                 => $schedule->accomodation,
            'accomodation_details'         => $schedule->accomodation_details,
            'travel'                       => $schedule->travel,
            'travel_details'               => $schedule->travel_details,
            'repeat'                       => $schedule->repeat,
            'repeat_every'                 => $schedule->repeat_every,
            'repeat_period'                => $schedule->repeat_period,
            'notice_min_time'              => $schedule->notice_min_time,
            'notice_min_period'            => $schedule->notice_min_period,
            'buffer_time'                  => $schedule->buffer_time,
            'buffer_period'                => $schedule->buffer_period,
            'address'                      => $schedule->address,
            'appointment'                  => $schedule->appointment,
            'created_at'                   => $schedule->created_at,
            'updated_at'                   => $schedule->updated_at,
            'venue_name'                   => $schedule->venue_name,
            'venue_address'                => $schedule->venue_address,
            'within_kilometers'            => $schedule->within_kilometers,
            'deleted_at'                   => $schedule->deleted_at,
            'is_published'                 => (bool)$schedule->is_published,
            'status'                       => $schedule->getCalculatedStatus(),
        ];
    }

    public function includeLocation(Schedule $schedule)
    {
        return $this->itemOrNull($schedule->location, new LocationTransformer());
    }

    public function includePrices(Schedule $schedule)
    {
        return $this->collectionOrNull($schedule->prices, new PriceTransformer());
    }

    public function includeService(Schedule $schedule)
    {
        return $this->itemOrNull($schedule->service, new ServiceTransformer());
    }

    public function includeCountry(Schedule $schedule)
    {
        return $this->itemOrNull($schedule->country, new CountryTransformer());
    }

    public function includeUsers(Schedule $schedule)
    {
        return $this->collectionOrNull($schedule->users, new UserTransformer());
    }

    public function includeMediaFiles(Schedule $schedule)
    {
        return $this->collectionOrNull($schedule->media_files, new MediaFileTransformer());
    }

    public function includeScheduleAvailabilities(Schedule $schedule)
    {
        return $this->collectionOrNull($schedule->schedule_availabilities, new ScheduleAvailabilityTransformer());
    }

    public function includeScheduleUnavailabilities(Schedule $schedule)
    {
        return $this->collectionOrNull($schedule->schedule_unavailabilities, new ScheduleUnavailabilityTransformer());
    }

    public function includeScheduleFiles(Schedule $schedule)
    {
        return $this->collectionOrNull($schedule->schedule_files, new ScheduleFileTransformer());
    }

    public function includeScheduleHiddenFiles(Schedule $schedule)
    {
        return $this->collectionOrNull($schedule->schedule_hidden_files, new ScheduleHiddenFileTransformer());
    }

    public function includeBookings(Schedule $schedule)
    {
        return $this->collectionOrNull($this->bookings ?: $schedule->bookings, new BookingTransformer());
    }

    public function includeRescheduleRequests(Schedule $schedule)
    {
        return $this->collectionOrNull($schedule->reschedule_requests, new RescheduleRequestTransformer());
    }
}
