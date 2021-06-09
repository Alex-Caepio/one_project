<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Http\Requests\StripeRequest;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller {

    public function handler(Request $request) {
        $stripeRequest = new StripeRequest($request);
        $actionClass = $stripeRequest->getRequestHandler();
        if (!$actionClass) {
            abort(404);
        }
        Log::info('Event Handler for Stripe request: '.$actionClass);
        run_action($actionClass, $stripeRequest);
        return response('', 200);
    }





}
