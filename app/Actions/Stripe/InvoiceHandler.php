<?php

namespace App\Actions\Stripe;

use App\Http\Requests\StripeRequest;
use Illuminate\Support\Facades\Log;

class InvoiceHandler {

    /**
     * @param \App\Http\Requests\StripeRequest $request
     */
    public function execute(StripeRequest $request): void {
        Log::info('[[' . __METHOD__ . ']]: start handle Invoice Event: ' . $request->getEventName());
    }

}
