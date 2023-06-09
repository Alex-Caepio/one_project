<?php

namespace App\Transformers;

use App\Models\Booking;
use App\Models\BookingSnapshot;

class BookingTransformer extends Transformer
{
    protected $availableIncludes = [
        'schedule',
        'schedule_snapshot',
        'user',
        'practitioner',
        'price',
        'schedule_availability',
        'purchase',
        'cancellation',
        'reschedule_requests',
        'client_reschedule_request',
        'practitioner_reschedule_request',
    ];

    /**
     * A Fractal transformer.
     */
    public function transform(Booking $booking): array
    {
        $result = [
            'id' => $booking->id,
            'reference' => $booking->reference,
            'user_id' => $booking->user_id,
            'schedule_id' => $booking->schedule_id,
            'price_id' => $booking->price_id,
            'availability_id' => $booking->availability_id,
            'datetime_from' => $booking->datetime_from,
            'datetime_to' => $booking->datetime_to,
            'quantity' => $booking->quantity,
            'cost' => $booking->cost,
            'promocode_id' => $booking->promocode_id,
            'purchase_id' => $booking->purchase_id,
            'created_at' => $booking->created_at,
            'updated_at' => $booking->updated_at,
            'cancelled_at' => $booking->cancelled_at,
            'status' => $booking->status,
            'refund_terms' => $booking->refund_terms,
            'amount' => $booking->amount,
            'discount' => $booking->discount,
            'is_installment' => $booking->is_installment,
            'installment_paid' => $booking->is_installment ? $this->getInstallmentPaidAmount($booking) : 0,
            'is_fully_paid' => $booking->is_fully_paid,
            'is_active' => $booking->isActive(),
            'completed_at' => $booking->completed_at
        ];

        if ($booking instanceof BookingSnapshot) {
            $result = array_merge($result, [
                'id' => $booking->booking->id,
                'schedule_id' => $booking->booking->schedule()->withTrashed()->first()->id,
                'datetime_from' => $booking->booking->datetime_from,
                'datetime_to' => $booking->booking->datetime_to,
                'purchase_id' => $booking->booking->purchase->id,
                'cancelled_at' => $booking->booking->cancelled_at,
                'status' => $booking->booking->status,
                'refund_terms' => $booking->refund_terms ?? $booking->booking->refund_terms,
            ]);
        }

        return $result;
    }

    public function includeSchedule(Booking $booking)
    {
        return $this->itemOrNull($this->getSchedule($booking), new ScheduleTransformer());
    }

    public function includeUser(Booking $booking)
    {
        return $this->itemOrNull($booking->user, new UserTransformer());
    }

    public function includePurchase(Booking $booking)
    {
        return $this->itemOrNull($booking->purchase, new PurchaseTransformer());
    }

    public function includePractitioner(Booking $booking)
    {
        return $this->itemOrNull($booking->practitioner, new UserTransformer());
    }

    public function includePrice(Booking $booking)
    {
        return $this->itemOrNull($booking->price, new PriceTransformer());
    }

    public function includeScheduleAvailabilities(Booking $booking)
    {
        return $this->itemOrNull($booking->schedule_availability, new ScheduleAvailabilityTransformer());
    }

    public function includeCancellation(Booking $booking)
    {
        return $this->itemOrNull($booking->cancellation, new CancellationTransformer());
    }

    public function includeRescheduleRequests(Booking $booking)
    {
        return $this->collectionOrNull($booking->reschedule_requests, new RescheduleRequestTransformer());
    }

    public function includePractitionerRescheduleRequest(Booking $booking)
    {
        return $this->itemOrNull($booking->practitioner_reschedule_request, new RescheduleRequestTransformer());
    }

    public function includeClientRescheduleRequest(Booking $booking)
    {
        return $this->itemOrNull($booking->client_reschedule_request, new RescheduleRequestTransformer());
    }

    private function getInstallmentPaidAmount(Booking $booking): float
    {
        if (is_null($booking->purchase->instalments())) {
            return 0;
        }

        return $booking
            ->purchase
            ->instalments()
            ->whereIsPaid(true)
            ->get('payment_amount')
            ->sum('payment_amount');
    }

    private function getSchedule(Booking $booking)
    {
        return isset($booking->snapshot) ? $booking->snapshot->schedule : $booking->schedule;
    }
}
