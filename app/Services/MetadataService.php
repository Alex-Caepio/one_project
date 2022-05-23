<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\User;

class MetadataService
{
    public static function retrieveMetadataPurchase(Purchase $purchase): array
    {
        $references = [];
        foreach ($purchase->bookings()->get()->unique('reference') as $booking) {
            $references[] = $booking->reference;
        }
        $referenceStr = implode(',', $references);

        return [
            'Practitioner business email' => $purchase->service->practitioner->business_email,
            'Practitioner business name' => $purchase->service->practitioner->business_name,
            'Practitioner stripe id' => $purchase->service->practitioner->stripe_customer_id,
            'Practitioner connected account id' => $purchase->service->practitioner->stripe_account_id,
            'Tom Commission' => $purchase->service->practitioner->getCommission() . '%',
            'Application Fee' =>
                round($purchase->price * $purchase->service->practitioner->getCommission() / 100, 2, PHP_ROUND_HALF_DOWN)
                . '(' . config('app.platform_currency') . ')',
            'Client first name' => $purchase->user->first_name,
            'Client last name' => $purchase->user->last_name,
            'Client stripe id' => $purchase->user->stripe_customer_id,
            'Booking reference' => $referenceStr,
            'Promoted by' => $purchase->promocode->promotion->applied_to ?? "",
        ];
    }

    public static function retrieveMetadataSubscription(User $user): array
    {
        return [
            'Type' => 'Subscription update',
            'Practitioner business email' => $user->business_email,
            'Practitioner business name' => $user->business_name,
            'Practitioner stripe id' => $user->stripe_customer_id,
            'Practitioner connected account id' => $user->stripe_account_id,
        ];
    }
}
