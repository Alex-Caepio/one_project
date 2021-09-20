<?php


namespace App\Transformers;

use App\Models\Purchase;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class PurchaseTransformer extends Transformer
{
    protected $availableIncludes = [
        'schedule',
        'user',
        'price',
        'service',
        'promocode',
        'instalments',
        'cancellations',
        'bookings'
    ];

    /**
     * A Fractal transformer.
     *
     * @param \App\Models\Purchase $purchase
     * @return array
     */
    public function transform(Purchase $purchase): array
    {
        return [
            'id'                        => $purchase->id,
            'reference'                 => $purchase->reference,
            'booking_reference'         => $purchase->bookings()->first()->reference ?? null,
            'user_id'                   => $purchase->user_id,
            'schedule_id'               => $purchase->schedule_id,
            'service_id'                => $purchase->service_id,
            'price_id'                  => $purchase->price_id,
            'promocode_id'              => $purchase->promocode_id,
            'price_original'            => $purchase->price_original,
            'price'                     => $purchase->price,
            'created_at'                => $purchase->created_at,
            'updated_at'                => $purchase->updated_at,
            'deleted_at'                => $purchase->deleted_at,
            'cancelled_at_subscription' => $purchase->cancelled_at_subscription,
            'is_deposit'                => $purchase->is_deposit,
            'deposit_amount'            => $purchase->deposit_amount,
            'stripe_id'                 => $purchase->stripe_id,
            'amount'                    => $purchase->amount,
            'discount'                  => $purchase->discount,
            'discount_applied'          => $purchase->discount_applied,
            'subscription_id'           => $purchase->subscription_id,
        ];
    }

    public function includeSchedule(Purchase $purchase): ?Item
    {
        return $this->itemOrNull($purchase->schedule, new ScheduleTransformer());
    }

    public function includeService(Purchase $purchase): ?Item
    {
        return $this->itemOrNull($purchase->service, new ServiceTransformer());
    }

    public function includeUser(Purchase $purchase): ?Item
    {
        return $this->itemOrNull($purchase->user, new UserTransformer());
    }

    public function includePrice(Purchase $purchase): ?Item
    {
        return $this->itemOrNull($purchase->price, new PriceTransformer());
    }

    public function includePromocode(Purchase $purchase): ?Item
    {
        return $this->itemOrNull($purchase->promocode, new PromotionCodeTransformer());
    }

    public function includeInstalments(Purchase $purchase): ?Collection
    {
        return $this->collectionOrNull($purchase->instalments, new InstalmentTransformer());
    }

    public function includeCancellations(Purchase $purchase): ?Collection
    {
        return $this->collectionOrNull($purchase->cancellations, new CancellationTransformer());
    }

    public function includeBookings(Purchase $purchase): ?Collection
    {
        return $this->collectionOrNull($purchase->bookings, new BookingTransformer());
    }

}
