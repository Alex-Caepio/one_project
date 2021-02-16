<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\DeleteUser;
use App\Filters\UserFiltrator;
use App\Models\User;
use App\Mail\VerifyEmail;
use App\Http\Requests\Request;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;
use App\Http\Requests\Auth\RegisterRequest;
use App\Actions\Stripe\CreateStripeUserByEmail;
use App\Http\Requests\Admin\PractitionerShowRequest;
use App\Http\Requests\Admin\PractitionerUpdateRequest;
use App\Http\Requests\Admin\PractitionerDestroyRequest;
use App\Actions\Practitioners\CreatePractitionerFromRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class PractitionerController extends Controller {
    public function index(Request $request) {
        $userQuery = User::where('account_type', User::ACCOUNT_PRACTITIONER);
        $userFilter = new UserFiltrator();
        $userFilter->apply($userQuery, $request);

        $includes = $request->getIncludes();

        $paginator = $userQuery->with($includes)->paginate($request->getLimit());
        $practitioners = $paginator->getCollection();
        return response(fractal($practitioners,
                                new UserTransformer())->parseIncludes($includes))->withPaginationHeaders($paginator);
    }

    public function store(RegisterRequest $request) {
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

    protected function sendVerificationEmail($user) {
        $linkApi = URL::temporarySignedRoute('verify-email', now()->addMinute(60), [
            'user'  => $user->id,
            'email' => $user->email
        ]);

        $linkFrontend = config('app.frontend_password_reset_link') . '?' . explode('?', $linkApi)[1];

        Mail::to([
                     'email' => $user->email
                 ])->send(new VerifyEmail($linkFrontend));
    }

    public function show(User $practitioner, PractitionerShowRequest $request) {
        return fractal($practitioner, new UserTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function update(PractitionerUpdateRequest $request, User $practitioner) {
        $practitioner->forceFill($request->all());
        $practitioner->save();
        return fractal($practitioner, new UserTransformer())->respond();
    }

    public function destroy(User $practitioner, PractitionerDestroyRequest $request) {
        run_action(DeleteUser::class, $practitioner, $request);
        return response(null, 204);
    }

    public function unpublished(User $practitioner) {
        $practitioner->forceFill([
                                     'is_published' => false,
                                 ]);
        $practitioner->update();

    }

    public function publish(User $practitioner) {
        $practitioner->forceFill([
                                     'is_published' => true,
                                 ]);
        $practitioner->update();
    }
}
