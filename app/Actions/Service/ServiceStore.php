<?php

namespace App\Actions\Service;

use App\Http\Requests\Services\StoreServiceRequest;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class ServiceStore extends ServiceAction
{
    /**
     * @param \App\Http\Requests\Services\StoreServiceRequest $request
     * @param \Stripe\StripeClient $stripeClient
     * @return \App\Models\Service
     */
    public function execute(StoreServiceRequest $request, StripeClient  $stripeClient): ?Service
    {
        try {
            $stripeProduct = $stripeClient->products->create(['name' => $request->title]);
            $service = new Service();
            $service->stripe_id = $stripeProduct->id;
            $service->user_id = Auth::id();
            if ($request->is_published) {
                $service->published_at = now();
            }

            $this->saveService($service, $request);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::channel('stripe_product_errors')->error("Client could not create product", [
                'user_id' => $request->user_id,
                'stripe_product'  => $stripeProduct->id ?? null,
                'name' => $request->title,
                'message' => $e->getMessage(),
            ]);
            return null;
        }

        Log::channel('stripe_product_success')->info("Client created product", [
            'user_id' => $request->user_id,
            'stripe_product'  => $stripeProduct->id,
            'name' => $request->title,
        ]);

        return $service;
    }
}
