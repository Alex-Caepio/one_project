<?php

namespace App\Actions\Client;

use App\Actions\Cancellation\CancelBooking;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Cancels active client bookings.
 */
class CancelClientBookings
{
    public function execute(User $user): void
    {
        foreach ($user->bookings()->active()->get() as $booking) {
            try {
                run_action(CancelBooking::class, $booking, false, User::ACCOUNT_CLIENT);
            } catch (\Exception $e) {
                Log::channel('practitioner_cancel_error')->warning('[[Cancellation on unpublish failed]]: ', [
                    'user_id' => $booking->user_id ?? null,
                    'practitioner_id' => $booking->practitioner_id ?? null,
                    'booking_id' => $booking->id ?? null,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }
}
