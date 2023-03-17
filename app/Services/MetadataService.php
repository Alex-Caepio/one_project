<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\User;
use App\Models\Plan;

class MetadataService
{
    const TYPE_SUBSCRIPTION_UPDATE = 'Subscription update';
    const TYPE_SUBSCRIPTION_INIT = 'Subscription init';
    const TYPE_PURCHASE = 'Purchase';
    const TYPE_INSTALLMENT = 'Installment Purchase';
    const TYPE_DEPOSIT = 'Purchase deposit';

    public static function retrieveMetadataPurchase(Purchase $purchase, string $type = self::TYPE_PURCHASE, string $sourceChargeId = ''): array
    {
        $references = [];
        foreach ($purchase->bookings()->get()->unique('reference') as $booking) {
            $references[] = $booking->reference;
        }
        $referenceStr = implode(',', $references);

        $metadata = [
            'Type' => $type,
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

        if ($sourceChargeId) {
            $metadata = array_merge($metadata, ['Source charge id' => $sourceChargeId]);
        }

        return $metadata;
    }

    public static function retrieveMetadataSubscription(User $user): array
    {
        return [
            'Type' => self::TYPE_SUBSCRIPTION_UPDATE,
            'Practitioner business email' => $user->business_email,
            'Practitioner business name' => $user->business_name,
            'Practitioner stripe id' => $user->stripe_customer_id,
            'Practitioner connected account id' => $user->stripe_account_id,
        ];
    }

    public static function retrieveMetadataSubscriptionInit(User $user, Plan $plan = null): array
    {
        $newPlan = !empty($user->plan) ? $user->plan : (!empty($plan) ? $plan : '');

        return [
            'Type' => self::TYPE_SUBSCRIPTION_INIT,
            'Practitioner plan' => $newPlan->name,
            'Plan cost' => round($newPlan->price, 2, PHP_ROUND_HALF_DOWN)
                . '(' . config('app.platform_currency') . ')',
            'Practitioner business email' => $user->business_email,
            'Practitioner business name' => $user->business_name,
            'Practitioner stripe id' => $user->stripe_customer_id,
            'Practitioner connected account id' => $user->stripe_account_id,
        ];
    }
}
