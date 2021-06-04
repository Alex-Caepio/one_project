<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Http\Requests\Stripe\StripeConnectedRequest;
use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class StripeAccountController extends Controller {
    public function link(Request $request, StripeClient $stripe) {
        return $stripe->accountLinks->create([
                                                 'account'     => $request->user()->stripe_account_id,
                                                 'refresh_url' => config('app.frontend_stripe_account_refresh'),
                                                 'return_url'  => config('app.frontend_stripe_account_redirect_back'),
                                                 'type'        => 'account_onboarding',
                                             ]);
    }

    public function account(Request $request, StripeClient $stripe) {
        return $stripe->accounts->retrieve($request->user()->stripe_account_id);
    }


    public function stripeConnected(StripeConnectedRequest $request, StripeClient $stripeClient) {
        $user = Auth::user();
        // Retrieve AccontDetails from Stripe
        try {
            $account = $stripeClient->accounts->retrieve($user->stripe_account_id);
            if ($account->details_submitted) {
                Log::channel('stripe_client_success')->info("Successfully connected: ", $account->toArray());
                $user->connected_at = now();
                $user->save();
                return fractal($user, new UserTransformer())
                    ->parseIncludes($request->getIncludes())->respond();
            } else {
                Log::channel('stripe_client_error')->info("User decline Stripe Connection", $account->toArray());
                throw new \Exception('Account submitted flag is FALSE');
            }
        } catch (\Exception $e) {
            Log::channel('stripe_client_error')->info("Cannot retrieve info regarding stripe account", [
                'user_id'    => $user->id,
                'email'      => $user->email,
                'message'    => $e->getMessage(),
                'account_id' => $user->stripe_account_id
            ]);
        }

        return response(null, 204);
    }

}
