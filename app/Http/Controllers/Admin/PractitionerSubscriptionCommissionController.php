<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Stripe\CreateSubscriptionSchedule;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PractitionerSubscriptionCommissionRequest;
use App\Transformers\PractitionerSubscriptionCommissionTransformer;
use App\Http\Requests\Request;
use App\Models\PractitionerSubscriptionCommission;
use Illuminate\Http\Exceptions\HttpResponseException;
use Stripe\Exception\InvalidRequestException;
use Stripe\StripeClient;

class PractitionerSubscriptionCommissionController extends Controller
{
    public function index(Request $request)
    {
        $practitionerSubscriptionCommission = PractitionerSubscriptionCommission::query();

        $includes = $request->getIncludes();
        $paginator = $practitionerSubscriptionCommission->with($includes)
            ->paginate($request->getLimit());

        $practitionerSubscriptionCommission = $paginator->getCollection();

        return response(
            fractal($practitionerSubscriptionCommission, new PractitionerSubscriptionCommissionTransformer())
                ->parseIncludes($request->getIncludes())
                ->toArray()
        )
            ->withPaginationHeaders($paginator);
    }

    public function show(Request $request, PractitionerSubscriptionCommission $subscriptionCommission)
    {
        return fractal($subscriptionCommission, new PractitionerSubscriptionCommissionTransformer())
            ->parseIncludes($request->getIncludes());
    }

    public function store(PractitionerSubscriptionCommissionRequest $request)
    {
        $practitionerCommission = new PractitionerSubscriptionCommission();

        $subscriptionSchedule = run_action(CreateSubscriptionSchedule::class, $request);

        $data = $request->all();
        $data['stripe_coupon_id'] = $subscriptionSchedule->phases[0]->coupon;
        $data['subscription_schedule_id'] = $subscriptionSchedule->id;

        $practitionerCommission->fill($data);
        $practitionerCommission->save();

        return fractal($practitionerCommission, new PractitionerSubscriptionCommissionTransformer())->respond();
    }

    public function update(
        Request $request,
        PractitionerSubscriptionCommission $subscriptionCommission,
        StripeClient $stripe
    ) {
        $data = $request->all();

        if ($this->hasChanges($request, $subscriptionCommission)) {
            $stripe->subscriptionSchedules->cancel(
                $subscriptionCommission->subscription_schedule_id,
                []
            );

            $stripe->coupons->delete(
                $subscriptionCommission->stripe_coupon_id,
                []
            );

            $subscriptionSchedule = run_action(CreateSubscriptionSchedule::class, $request);

            $data['stripe_coupon_id'] = $subscriptionSchedule->phases[0]->coupon;
            $data['subscription_schedule_id'] = $subscriptionSchedule->id;
        }

        $subscriptionCommission->update($data);

        return fractal($subscriptionCommission, new PractitionerSubscriptionCommissionTransformer())->respond();
    }

    public function delete(PractitionerSubscriptionCommission $subscriptionCommission)
    {
        $subscriptionCommission->delete();

        return response(null, 204);
    }

    protected function hasChanges($request, $subscriptionCommission)
    {
        return $request->date_from != $subscriptionCommission->date_from
            || $request->user_id != $subscriptionCommission->user_id
            || $request->rate != $subscriptionCommission->rate
            || $request->date_to != $subscriptionCommission->date_to
            || $request->is_dateless != $subscriptionCommission->is_dateless;
    }
}
