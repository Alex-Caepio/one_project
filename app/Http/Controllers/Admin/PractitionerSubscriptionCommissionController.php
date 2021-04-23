<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PractitionerSubscriptionCommissionRequest;
use App\Transformers\PractitionerSubscriptionCommissionTransformer;
use App\Http\Requests\Request;
use App\Models\PractitionerSubscriptionCommission;

class PractitionerSubscriptionCommissionController extends Controller
{
    public function index(Request $request)
    {
        $practitionerSubscriptionCommission = PractitionerSubscriptionCommission::query();

        $practitionerSubscriptionCommission->selectRaw('practitioner_subscription_commissions.*, plans.commission_on_sale')
            ->join('users', 'users.id', '=', 'practitioner_subscription_commissions.user_id')
            ->leftJoin('plans', 'plans.id', '=', 'users.plan_id');

        $includes  = $request->getIncludes();
        $paginator = $practitionerSubscriptionCommission->with($includes)
            ->paginate($request->getLimit());

        $practitionerSubscriptionCommission  = $paginator->getCollection();

        return response(fractal($practitionerSubscriptionCommission, new PractitionerSubscriptionCommissionTransformer())
            ->parseIncludes($request->getIncludes())->toArray())
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

        $data = $request->all();

        $practitionerCommission->fill($data);
        $practitionerCommission->save();

        return fractal($practitionerCommission, new PractitionerSubscriptionCommissionTransformer())->respond();
    }

    public function update(Request $request, PractitionerSubscriptionCommission $subscriptionCommission)
    {
        $data = $request->all();
        $subscriptionCommission->update($data);

        return fractal($subscriptionCommission, new PractitionerSubscriptionCommissionTransformer())->respond();
    }

    public function delete(PractitionerSubscriptionCommission $subscriptionCommission)
    {
        $subscriptionCommission->delete();

        return response(null, 204);
    }
}
