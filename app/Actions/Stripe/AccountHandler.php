<?php

namespace App\Actions\Stripe;

use App\Http\Requests\Request;
use App\Http\Requests\StripeRequest;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/*
 * Class AccountHandler
 * @package App\Actions\Stripe
 */
class AccountHandler {

    private ?User $account;

    /**
     * @param \App\Http\Requests\StripeRequest $request
     */
    public function execute(StripeRequest $request): void {
        Log::info('[[' . __METHOD__ . ']]: start handle Account Event: ' . $request->getType());
        $dataObject = $request->getObject();

    }


}
