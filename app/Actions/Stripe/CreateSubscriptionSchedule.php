<?php

namespace App\Actions\Stripe;

use App\Models\User;
use Carbon\Carbon;
use Stripe\StripeClient;

class CreateSubscriptionSchedule
{
    public function execute($request)
    {
        $stripe = app(StripeClient::class);
        $user = User::where('id', $request->user_id)->first();

        $endDate = $request->date_to;
        if($request->is_dateless) {
            $endDate = Carbon::parse($request->date_to)->addYear(19);
        }

        $coupon = $stripe->coupons->create([
            'amount_off' => $request->rate * 100,
            'currency' => config('app.platform_currency'),
            'duration' => 'forever'
        ]);

        $subscriptionSchedule = $stripe->subscriptionSchedules->create([
            'customer' => $user->stripe_customer_id,
            'start_date' => strtotime($request->date_from ?? now()),
            'end_behavior' => 'release',
            'phases' => [
                [
                    'items' => [
                        [
                            'price' => $user->plan->stripe_id,
                            'quantity' => 1,
                        ]
                    ],
                    'coupon' => $coupon->id,
                    'end_date' => strtotime($endDate),

                ],
            ],
        ]);

        return $subscriptionSchedule;
    }
}