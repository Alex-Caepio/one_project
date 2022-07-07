<?php

namespace App\Http\Controllers;

use App\Actions\Plan\CancelSubscription;
use App\Actions\Plan\FinalizeSubscription;
use App\Actions\Plan\UpdateSubscription;
use App\Http\Requests\Plans\FinalizeRequest;
use App\Http\Requests\Plans\PlanRequest;
use App\Http\Requests\Plans\PlanTrialRequest;
use App\Models\Plan;
use App\Http\Requests\Request;
use App\Models\ServiceType;
use App\Transformers\PlanTransformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $plans = Plan::query()
            ->where('is_private', false)
            ->when(!is_null(Auth::guard('sanctum')->id()), function (Builder $builder) {
                return $builder
                    ->orWhere(function (Builder $builder) {
                        return $builder
                            ->where('is_private', true)
                            ->whereHas('users', function (Builder $builder) {
                                return $builder
                                    ->whereId(Auth::guard('sanctum')->id())
                                    ->select('id');
                            });
                    });
            })
            ->orderBy('plans.order')
            ->get();
        $plans->map(static function (Plan $plan) {
            $serviceTypes =
                ServiceType::query()
                    ->join('plan_service_type', 'plan_service_type.service_type_id', '=', 'service_types.id')
                    ->where('plan_service_type.plan_id', '=', $plan->id)
                    ->orderByRaw("
                        CASE
                            WHEN plan_service_type.service_type_id = '{$plan::ORDER_OF_PLANS[1]}' THEN 1
                            WHEN plan_service_type.service_type_id = '{$plan::ORDER_OF_PLANS[2]}' THEN 2
                            WHEN plan_service_type.service_type_id = '{$plan::ORDER_OF_PLANS[3]}' THEN 3
                            WHEN plan_service_type.service_type_id = '{$plan::ORDER_OF_PLANS[4]}' THEN 4
                            WHEN plan_service_type.service_type_id = '{$plan::ORDER_OF_PLANS[5]}' THEN 5
                        END
                        ASC
                    ")
                    ->select('service_types.*')
                    ->get();
            $plan->setRelation('service_types', $serviceTypes);

            return $plan;
        });

        return fractal($plans, new PlanTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    /**
     * Try to purchase plan if possible or return token for 3ds auth
     *
     * @param Plan $plan
     * @param StripeClient $stripe
     * @param PlanRequest $request
     * @return mixed
     */
    public function purchase(Plan $plan, StripeClient $stripe, PlanRequest $request)
    {
        $user = Auth::user();
        $isNewPlan = empty($user->plan_id);
        run_action(CancelSubscription::class, $user, $stripe);

        return run_action(UpdateSubscription::class, $user, $stripe, $plan, $isNewPlan, $request);
    }

    public function finalize(Plan $plan, StripeClient $stripe, FinalizeRequest $request)
    {
        $user = Auth::user();
        $isNewPlan = empty($user->plan_id);

        $result = run_action(FinalizeSubscription::class, $user, $stripe, $plan, $isNewPlan, $request);

        if (!$result) {
            return response()->json([
                'errors' => [
                    'payment_method_id' => 'The payment could not be processed. Please check with your bank or choose another payment option.'
                ]
            ], 422);
        }

        return response('', 204);
    }

    public function purchaseFree(Plan $plan, StripeClient $stripe, PlanTrialRequest $request)
    {
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

        return $result;
    }
}
