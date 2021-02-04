<?php

namespace App\Transformers;

use App\Models\Booking;

class BookingTransformer extends Transformer {
    protected $availableIncludes = [
        'schedule',
        'user',
        'practitioner',
        'price',
        'schedule_availability',
        'purchase',
        'cancellation'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Booking $booking) {
        return [
            'id'              => $booking->id,
            'user_id'         => $booking->user_id,
            'schedule_id'     => $booking->schedule_id,
            'price_id'        => $booking->price_id,
            'availability_id' => $booking->availability_id,
            'datetime_from'   => $booking->datetime_from,
            'datetime_to'     => $booking->datetime_to,
            'quantity'        => $booking->quantity,
            'cost'            => $booking->cost,
            'promocode_id'    => $booking->promocode_id,
            'purchase_id'     => $booking->purchase_id,
            'created_at'      => $booking->created_at,
            'updated_at'      => $booking->updated_at,
        ];
    }

    public function includeSchedule(Booking $booking) {
        return $this->collectionOrNull($booking->schedule, new ScheduleTransformer());
    }

    public function includeUser(Booking $booking) {
        return $this->itemOrNull($booking->user, new UserTransformer());
    }

    public function includePractitioner(Booking $booking) {
        return $this->itemOrNull($booking->practitioner, new UserTransformer());
    }

    public function includePrices(Booking $booking) {
        return $this->itemOrNull($booking->price, new PriceTransformer());
    }

    public function includeScheduleAvailabilities(Booking $booking) {
        return $this->itemOrNull($booking->schedule_availability, new ScheduleAvailabilityTransformer());
    }

    public function includePurchases(Booking $booking) {
        return $this->itemOrNull($booking->purchase, new PurchaseTransformer());
    }

    public function includeCancellation(Booking $booking) {
        return $this->itemOrNull($booking->cancellation, new CancellationTransformer());
    }
}
