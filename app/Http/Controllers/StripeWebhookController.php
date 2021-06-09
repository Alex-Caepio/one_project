<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Http\Requests\StripeRequest;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller {

    public function handler(Request $request) {
        $stripeRequest = new StripeRequest($request);
        $actionClass = $stripeRequest->getRequestHandler();
        if ($actionClass) {
            run_action($actionClass, $stripeRequest);
            Log::channel('stripe_webhooks_success')->info('Webhook successfully handled: ', [
                'action'     => $actionClass
            ]);
        } else {
            Log::channel('stripe_webhooks_error')->info('Webhook is unhandled: ', [
                'action'     => $actionClass
            ]);
        }
        return response('', 200);
    }


}
