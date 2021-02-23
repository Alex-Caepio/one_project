<?php

namespace App\Observers;

use App\Events\BookingConfirmation;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingObserver {

    /**
     * Handle booking creation.
     *
     * @param \App\Models\Booking $booking
     * @return void
     */
    public function creating(Booking $booking): void {
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
    public function created(Booking $booking): void {
        event(new BookingConfirmation(Auth::user(), $booking, $booking->practitioner));
    }
}
