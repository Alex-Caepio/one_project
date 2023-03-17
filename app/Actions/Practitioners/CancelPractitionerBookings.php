<?php

namespace App\Actions\Practitioners;

use App\Actions\Cancellation\CancelBooking;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class CancelPractitionerBookings
{
    public function execute(User $practitioner): void
    {
        $bookings = $practitioner->practitioner_bookings()
            ->active()
            ->whereHas('schedule.service', function (Builder $query) {
                $query->whereIn('service_type_id', [
                    Service::TYPE_EVENT,
                    Service::TYPE_WORKSHOP,
                    Service::TYPE_RETREAT,
                    Service::TYPE_BESPOKE,
                ]);
                $query->orWhere([
                    ['service_type_id', '=', Service::TYPE_APPOINTMENT],
                    ['bookings.datetime_from', '>', Carbon::now()],
                ]);
            })
            ->get()
        ;

        foreach ($bookings as $booking) {
            try {
                run_action(CancelBooking::class, $booking, false, User::ACCOUNT_PRACTITIONER);
            } catch (Exception $e) {
                Log::channel('practitioner_cancel_error')->error('[[Cancellation on unpublish failed]]: ', [
                    'user_id' => $booking->user_id ?? null,
                    'practitioner_id' => $booking->practitioner_id ?? null,
                    'booking_id' => $booking->id ?? null,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }
}
