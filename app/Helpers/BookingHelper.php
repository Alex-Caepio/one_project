<?php

namespace App\Helpers;

use App\Models\Booking;

class BookingHelper {

    public static function generateReference(): string
    {
        do {
            $reference = unique_string(8);
        } while (Booking::where('reference', $reference)->exists());

        return $reference;
    }
}
