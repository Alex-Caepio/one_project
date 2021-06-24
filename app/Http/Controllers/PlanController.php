<?php

namespace App\Http\Controllers;

use App\Actions\Plan\CancelSubscription;
use App\Actions\Plan\UpdateSubscription;
use App\Events\AccountUpgradedToPractitioner;
use App\Events\ChangeOfSubscription;
use App\Helpers\UserRightsHelper;
use App\Http\Requests\Plans\PlanRequest;
use App\Events\SubscriptionConfirmation;
use App\Http\Requests\Plans\PlanTrialRequest;
use App\Models\Article;
use App\Models\Plan;
use App\Http\Requests\Request;
use App\Models\User;
use App\Transformers\PlanTransformer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class PlanController extends Controller {
    public function index(Request $request) {
        $plans = Plan::with('service_types')->get();
        return fractal($plans, new PlanTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function purchase(Plan $plan, StripeClient $stripe, PlanRequest $request) {
        $user = Auth::user();

        run_action(CancelSubscription::class, $user, $stripe);

        $result = run_action(UpdateSubscription::class, $user, $stripe, $plan, empty($user->plan_id), $request);

        if (!$result) {
            return response()->json([
                                        'errors' => [
                                            'payment_method_id' => 'The payment could not be processed. Please check with your bank or choose another payment option.'
                                        ]
                                    ], 422);
        }

        return response('', 204);

    }

    public function purchaseFree(Plan $plan, StripeClient $stripe, PlanTrialRequest $request) {
        $user = Auth::user();

        run_action(CancelSubscription::class, $user, $stripe);

        $result = run_action(UpdateSubscription::class, $user, $stripe, $plan, empty($user->plan_id), $request);

        if (!$result) {
            return response()->json([
                                        'errors' => [
                                            'payment_method_id' => 'The payment could not be processed. Please check with your bank or choose another payment option.'
                                        ]
                                    ], 422);
        }

        return response('', 204);

    }
}
