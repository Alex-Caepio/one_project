<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Practitioners\UnpublishPractitioner;
use App\Actions\Practitioners\UpdateMediaPractitioner;
use App\Filters\UserFiltrator;
use App\Http\Requests\Auth\PublishPractitionerRequest;
use App\Http\Requests\Auth\UnpublishPractitionerRequest;
use App\Http\Requests\Auth\UpdateMediaRequest;
use App\Models\User;
use App\Http\Requests\Request;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;
use App\Http\Requests\Auth\RegisterRequest;
use App\Actions\Stripe\CreateStripeUserByEmail;
use App\Http\Requests\Admin\PractitionerShowRequest;
use App\Http\Requests\Admin\PractitionerDestroyRequest;
use App\Actions\Practitioners\CreatePractitionerFromRequest;
use App\Actions\Practitioners\DeletePractitioner;

class PractitionerController extends Controller
{
    public function index(Request $request)
    {
        $userQuery = User::where('account_type', User::ACCOUNT_PRACTITIONER);
        $userFilter = new UserFiltrator();
        $userFilter->apply($userQuery, $request);

        $includes = $request->getIncludes();

        $paginator = $userQuery->with($includes)->paginate($request->getLimit());
        $practitioners = $paginator->getCollection();
        return response(fractal(
            $practitioners,
            new UserTransformer()
        )->parseIncludes($includes))->withPaginationHeaders($paginator);
    }

    public function store(RegisterRequest $request)
    {
        $customer = run_action(CreateStripeUserByEmail::class, $request->email);
        $user = run_action(CreatePractitionerFromRequest::class, $request, [
            'stripe_customer_id' => $customer->id,
            'is_admin'           => null,
            'account_type'       => User::ACCOUNT_PRACTITIONER
        ]);

        $token = $user->createToken('access-token');
        $user->withAccessToken($token);

        return fractal($user, new UserTransformer())->parseIncludes('access_token')->respond();
    }

    public function show(User $practitioner, PractitionerShowRequest $request)
    {
        return fractal($practitioner, new UserTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function update(UpdateMediaRequest $request, User $practitioner)
    {
        run_action(UpdateMediaPractitioner::class, $practitioner, $request);
        return fractal($practitioner, new UserTransformer())->respond();
    }

    public function destroy(User $practitioner, PractitionerDestroyRequest $request)
    {
        run_action(DeletePractitioner::class, $practitioner, $request->message);
        return response(null, 204);
    }

    public function unpublish(User $practitioner, UnpublishPractitionerRequest $request)
    {
        run_action(UnpublishPractitioner::class, $practitioner, $request->cancel_bookings);
        return response(null, 204);
    }

    public function publish(User $practitioner, PublishPractitionerRequest $request)
    {
        $practitioner->forceFill([
            'is_published' => true,
        ]);
        $practitioner->update();
        return response(null, 204);
    }
}
