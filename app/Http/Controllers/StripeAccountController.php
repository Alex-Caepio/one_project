<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Http\Requests\Stripe\StripeConnectedRequest;
use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Account;
use Stripe\StripeClient;

class StripeAccountController extends Controller {

    public function link(Request $request, StripeClient $stripe) {
        if (!empty($request->user()->stripe_account_id)) {
            return $stripe->accountLinks->create([
                                                     'account'     => $request->user()->stripe_account_id,
                                                     'refresh_url' => config('app.frontend_stripe_account_refresh'),
                                                     'return_url'  => config('app.frontend_stripe_account_redirect_back'),
                                                     'type'        => 'account_onboarding',
                                                 ]);
        }
        return response('', 204);
    }

    public function account(Request $request, StripeClient $stripe) {
        try {
            $account = $stripe->accounts->retrieve(Auth::user()->stripe_account_id);
            $this->setConnected($account);
        } catch (\Exception $e) {
            Log::channel('stripe_client_error')->info("Cannot retrieve info regarding stripe account", [
                'user_id'    => Auth::user()->id,
                'email'      => Auth::user()->email,
                'message'    => $e->getMessage(),
                'account_id' => Auth::user()->stripe_account_id
            ]);
        }
        return $account;
    }


    public function stripeConnected(StripeConnectedRequest $request, StripeClient $stripeClient) {
        // Retrieve AccontDetails from Stripe
        try {
            $account = $stripeClient->accounts->retrieve(Auth::user()->stripe_account_id);
            $this->setConnected($account);
            return fractal(Auth::user(), new UserTransformer())
                ->parseIncludes($request->getIncludes())->respond();
        } catch (\Exception $e) {
            Log::channel('stripe_client_error')->info("Cannot retrieve info regarding stripe account", [
                'user_id'    => Auth::user()->id,
                'email'      => Auth::user()->email,
                'message'    => $e->getMessage(),
                'account_id' => Auth::user()->stripe_account_id
            ]);
        }

        return response(null, 204);
    }

    private function setConnected(Account $account) {
        if ($account->details_submitted) {
            if (!Auth::user()->connected_at) {
                Log::channel('stripe_client_success')->info("Successfully connected: ", $account->toArray());
                Auth::user()->connected_at = now();
                Auth::user()->save();
            }
        } else {
            Log::channel('stripe_client_error')->info("User decline Stripe Connection", $account->toArray());
            throw new \Exception('Account submitted flag is FALSE');
        }
    }


}
