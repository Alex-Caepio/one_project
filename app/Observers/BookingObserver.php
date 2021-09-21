<?php

namespace App\Observers;

use App\Events\BookingConfirmation;
use App\Events\BookingDeposit;
use App\Models\Booking;
use App\Models\RescheduleRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingObserver
{

    /**
     * Handle booking creation.
     *
     * @param \App\Models\Booking $booking
     * @return void
     */
    public function creating(Booking $booking): void
    {
        if (!$booking->reference) {
            do {
                $reference = unique_string(8);
            } while (Booking::where('reference', $reference)->exists());
            $booking->reference = $reference;
        }
    }

    /**
     * Handle booking created.
     *
     * @param \App\Models\Booking $booking
     * @return void
     */
    public function created(Booking $booking): void
    {
        if (!$booking->is_installment) {
            event(new BookingConfirmation($booking));
        }
    }


    /**
     * Handle booking update.
     *
     * @param \App\Models\Booking $booking
     * @return void
     */
    public function saved(Booking $booking): void
    {
        if ($booking->isDirty('status') && !$booking->isActive()) {
            RescheduleRequest::where('booking_id', $booking->id)->delete();
        }
    }


    /**
     * @param \App\Models\Booking $booking
     */
    public function instalment_complete(Booking $booking): void
    {
        event(new BookingDeposit($booking));
    }

}
