<?php

namespace App\Observers;

use App\Events\BookingConfirmation;
use App\Events\BookingDeposit;
use App\Helpers\BookingHelper;
use App\Models\Booking;
use App\Models\RescheduleRequest;

class BookingObserver
{

    /**
     * Handle booking creation.
     *
     * @param Booking $booking
     * @return void
     */
    public function creating(Booking $booking): void
    {
        if (!$booking->reference) {
            $booking->reference = BookingHelper::generateReference();
        }
    }

    /**
     * Handle booking created.
     *
     * @param Booking $booking
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
     * @param Booking $booking
     * @return void
     */
    public function saved(Booking $booking): void
    {
        if ($booking->isDirty('status') && !$booking->isActive()) {
            RescheduleRequest::where('booking_id', $booking->id)->delete();
        }
    }


    /**
     * @param Booking $booking
     */
    public function instalment_complete(Booking $booking): void
    {
        event(new BookingDeposit($booking));
    }

}
