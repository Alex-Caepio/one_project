<?php

namespace App\Http\Controllers;

use App\Actions\Plan\CancelSubscription;
use App\Actions\Plan\UpdateSubscription;
use App\Http\Requests\Plans\PlanRequest;
use App\Http\Requests\Plans\PlanTrialRequest;
use App\Models\Plan;
use App\Http\Requests\Request;
use App\Models\ServiceType;
use App\Transformers\PlanTransformer;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class PlanController extends Controller {
    public function index(Request $request) {
        $plans = Plan::orderBy('plans.order', 'asc')->get();
        $plans->map(static function(Plan $plan) {
            $serviceTypes =
                ServiceType::join('plan_service_type', 'plan_service_type.service_type_id', '=', 'service_types.id')
                                       ->where('plan_service_type.plan_id', '=', $plan->id)
                ->orderBy('plan_service_type.id', 'ASC')->select('service_types.*')->get();
            $plan->setRelation('service_types', $serviceTypes);
            return $plan;
        });

        return fractal($plans, new PlanTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function purchase(Plan $plan, StripeClient $stripe, PlanRequest $request) {
        $user = Auth::user();
        $isNewPlan = empty($user->plan_id);
        run_action(CancelSubscription::class, $user, $stripe);

        $result = run_action(UpdateSubscription::class, $user, $stripe, $plan, $isNewPlan, $request);

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
        $isNewPlan = empty($user->plan_id);
        run_action(CancelSubscription::class, $user, $stripe);

        $result = run_action(UpdateSubscription::class, $user, $stripe, $plan, $isNewPlan, $request);

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
