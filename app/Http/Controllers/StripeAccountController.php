<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use Stripe\StripeClient;

class StripeAccountController extends Controller
{
    public function link(Request $request, StripeClient $stripe)
    {
        return $stripe->accountLinks->create([
            'account'     => $request->user()->stripe_account_id,
            'refresh_url' => config('app.frontend_stripe_account_refresh'),
            'return_url'  => config('app.frontend_stripe_account_redirect_back'),
            'type'        => 'account_onboarding',
        ]);
    }

    public function account(Request $request, StripeClient $stripe)
    {
        return $stripe->accounts->retrieve($request->user()->stripe_account_id);
    }
}
