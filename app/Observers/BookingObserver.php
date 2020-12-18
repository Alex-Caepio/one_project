<?php

namespace App\Observers;

use App\Models\Booking;
use Illuminate\Support\Str;

class BookingObserver {

    /**
     * Handle booking creation.
     *
     * @param \App\Models\Booking $booking
     * @return void
     */
    public function creating(Booking $booking) {
        if (!$booking->reference) {
            do {
                $reference = unique_string(8);
            } while (Booking::where('reference', $reference)->exists());
            $booking->reference = $reference;
        }
    }


}
