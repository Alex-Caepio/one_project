<?php

namespace App\Actions\Schedule;

use App\Events\ServiceUpdatedByPractitionerContractual;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\RescheduleRequest;
use App\Models\Schedule;
use Carbon\Carbon;

class CreateRescheduleRequestsOnScheduleUpdate {

    private Schedule $schedule;
    private array $changesList;

    public function execute(Schedule $schedule) {
        $this->schedule = $schedule;
        $this->changesList = $this->schedule->getRealChangesList();
        if ($this->requiresReschedule()) {
            $this->proceedRescheduleRequests();
        }
    }

    private function proceedRescheduleRequests(): void {
        //should be moved to constant
        $bookings = $this->getBookings();
        if ($bookings->count()) {
            //In order to avoid duplicated reschedule request we have to delete all previous first
            $this->schedule->rescheduleRequests()->whereIn('booking_id', $bookings->pluck('id')->toArray())->delete();

            $rescheduleRequests = [];
            foreach ($bookings as $booking) {
                $rescheduleRequests[] = [
                    'user_id'         => $booking->user_id,
                    'booking_id'      => $booking->id,
                    'schedule_id'     => $booking->schedule_id,
                    'new_schedule_id' => $this->schedule->id,
                    'created_at'      => Carbon::now()->format('Y-m-d H:i:s'),
                    'requested_by'    => 'practitioner'
                ];
            }

            if ($this->locationHasChanged() && isset($this->changesList['location_displayed'])) {
                foreach ($rescheduleRequests as $key => $reschedule) {
                    $rescheduleRequests[$key]['old_location_displayed'] = $this->schedule->getOriginal('location_displayed');
                    $rescheduleRequests[$key]['new_location_displayed'] = $this->changesList['location_displayed'];
                }
            }

            if ($this->dateHasChanged()) {
                foreach ($rescheduleRequests as $key => $reschedule) {
                    $rescheduleRequests[$key]['old_start_date'] = $this->schedule->getOriginal('start_date');
                    $rescheduleRequests[$key]['new_start_date'] = $this->schedule->start_date;
                    $rescheduleRequests[$key]['old_end_date'] = $this->schedule->getOriginal('end_date');
                    $rescheduleRequests[$key]['new_end_date'] = $this->schedule->end_date;
                }
            }
            // without events handler
            RescheduleRequest::insert($rescheduleRequests);
            event(new ServiceUpdatedByPractitionerContractual($this->schedule));
        }
    }


    private function getBookings(): ?Collection {
        return $this->schedule->service->service_type ===
               'appointment' ? $this->schedule->getOutsiderBookings() : Booking::where('schedule_id',
                                                                                       $this->schedule->id)
                                                                               ->whereNotIn('status',
                                                                                            ['completed', 'canceled'])
                                                                               ->get();
    }


    protected function requiresReschedule(): bool {
        return $this->dateHasChanged() || $this->locationHasChanged();
    }

    protected function dateHasChanged(): bool {
        return isset($this->changesList['start_date']) || isset($this->changesList['end_date']);
    }

    protected function locationHasChanged(): bool {
        return isset($this->changesList['venue']) ||
               isset($this->changesList['city']) || isset($this->changesList['country_id'])
               || isset($this->changesList['location_displayed']);
    }


}
