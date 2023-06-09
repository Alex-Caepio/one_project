<?php

namespace App\Actions\Schedule;

use App\Events\ServiceUpdatedByPractitionerContractual;
use App\Models\Country;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Booking;
use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\Service;
use Carbon\Carbon;

class CreateRescheduleRequestsOnScheduleUpdate
{
    private Schedule $schedule;

    private array $changesList;

    public function execute(Schedule $schedule)
    {
        $this->schedule = $schedule;
        $this->changesList = $this->schedule->getRealChangesList();
        if ($this->requiresReschedule()) {
            $this->proceedRescheduleRequests();
        }
    }

    private function proceedRescheduleRequests(): void
    {
        //should be moved to constant
        $bookings = $this->getBookings();
        if ($bookings->count()) {
            //In order to avoid duplicated reschedule request we have to delete all previous first
            $this->schedule->rescheduleRequests()->whereIn('booking_id', $bookings->pluck('id')->toArray())->delete();

            $rescheduleRequests = [];
            foreach ($bookings as $booking) {
                // If edit schedule locations and in the 'reschedule_requests' table
                // columns 'new_schedule_id' and 'schedule_id' have the same 'id' -
                // we can't get the old data from schedule,
                // but we need to display the old location of edited schedule
                if ($this->schedule->getOriginal('id') == $this->schedule->id) {
                    $oldLocationParams = ['venue_name', 'venue_address', 'city', 'country_id', 'post_code'];
                    $oldLocation = [];
                    foreach ($oldLocationParams as $param) {
                        if (!empty($this->schedule->getOriginal($param))) {
                            if ($param !== 'country_id') {
                                $oldLocation[] = $this->schedule->getOriginal($param);
                            } else {
                                $oldLocation[] = Country::find($this->schedule->getOriginal($param))->nicename;
                            }
                        }
                    }

                    $oldLocation = implode(", ", $oldLocation);
                }

                $rescheduleRequests[] = [
                    'user_id' => $booking->user_id,
                    'booking_id' => $booking->id,
                    'schedule_id' => $booking->schedule_id,
                    'new_schedule_id' => $this->schedule->id,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'requested_by' => RescheduleRequest::REQUESTED_BY_PRACTITIONER_IN_SCHEDULE,
                    'old_location_displayed' => $this->schedule->getOriginal('location_displayed'),
                    'new_location_displayed' => $this->changesList['location_displayed'] ?? null,
                    'old_location' => $oldLocation ?? null,
                    'old_start_date' => $this->schedule->getOriginal('start_date'),
                    'new_start_date' => $this->schedule->start_date,
                    'old_end_date' => $this->schedule->getOriginal('end_date'),
                    'new_end_date' => $this->schedule->end_date,
                    'old_url' => $this->schedule->getOriginal('url'),
                    'new_url' => $this->schedule->url,
                ];
            }
            // without events handler
            RescheduleRequest::insert($rescheduleRequests);
            event(new ServiceUpdatedByPractitionerContractual($this->schedule));
        }
    }

    /**
     * @return Collection|Booking[]|null
     */
    private function getBookings(): ?Collection
    {
        return $this->schedule->service->service_type === Service::TYPE_APPOINTMENT
            ? $this->schedule->getOutsiderBookings()
            : Booking::where('schedule_id', $this->schedule->id)
                ->whereNotIn('status', Booking::getInactiveStatuses())
                ->get();
    }


    protected function requiresReschedule(): bool
    {
        return $this->dateHasChanged() || $this->locationHasChanged();
    }

    protected function dateHasChanged(): bool
    {
        return isset($this->changesList['start_date']) || isset($this->changesList['end_date']);
    }

    protected function locationHasChanged(): bool
    {
        return isset($this->changesList['venue'])
            || isset($this->changesList['venue_address'])
            || isset($this->changesList['city'])
            || isset($this->changesList['post_code'])
            || isset($this->changesList['url'])
            || isset($this->changesList['country_id'])
            || isset($this->changesList['location_displayed']);
    }
}
