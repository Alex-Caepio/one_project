<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Http\Requests\StripeRequest;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handler(Request $request)
    {
        $stripeRequest = new StripeRequest($request);

        Log::channel('stripe_webhooks_info')
            ->info('Stripe WebHook Payload(' . $stripeRequest->getType(). ') : ', $stripeRequest->getObject());

        $actionClass = $stripeRequest->getRequestHandler();
        if ($actionClass !== null) {
            run_action($actionClass, $stripeRequest);
            Log::channel('stripe_webhooks_success')->info('Webhook successfully handled: ', [
                'action' => $actionClass
            ]);
        } else {
            Log::channel('stripe_webhooks_error')->warning('Webhook is unhandled: ', [
                'action'   => 'Undefined',
                'hookType' => $stripeRequest->getType()
            ]);
        }
        return response('', 200);
    }
}
