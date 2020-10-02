<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Practitioners\CreatePractitionerFromRequest;
use App\Actions\Stripe\CreateStripeUserByEmail;
use App\Events\AccountDeleted;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PractitionerDestroyRequest;
use App\Http\Requests\Admin\PractitionerShowRequest;
use App\Http\Requests\Admin\PractitionerUpdateRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Request;
use App\Mail\VerifyEmail;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Stripe\StripeClient;

class PractitionerController extends Controller
{
    public function index(Request $request)
    {
        $paginator = User::where('account_type', 'practitioner')->paginate($request->getLimit());
        $practitioner = $paginator->getCollection();
        return response(fractal($practitioner, new UserTransformer())->parseIncludes($request->getIncludes()))
            ->withPaginationHeaders($paginator);
    }

    public function store(RegisterRequest $request)
    {
        $customer = run_action(CreateStripeUserByEmail::class, $request->email);
        $user = run_action(CreatePractitionerFromRequest::class, $request, ['stripe_id' => $customer->id, 'is_admin' => null, 'account_type' => 'practitioner']);

        $token = $user->createToken('access-token');
        $user->withAccessToken($token);

       // $this->sendVerificationEmail($user);

        return fractal($user, new UserTransformer())
            ->parseIncludes('access_token')
            ->respond();
    }

    protected function sendVerificationEmail($user)
    {
        $linkApi = URL::temporarySignedRoute('verify-email', now()->addMinute(60), [
            'user' => $user->id,
            'email' => $user->email
        ]);

        $linkFrontend = config('app.frontend_password_reset_link') . '?' . explode('?', $linkApi)[1];

        Mail::to([
            'email' => $user->email
        ])->send(new VerifyEmail($linkFrontend));
    }

    public function show(User $practitioner, PractitionerShowRequest $request)
    {
        return fractal($practitioner, new UserTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function update(PractitionerUpdateRequest $request, User $practitioner)
    {
        $practitioner->update($request->all());
        return fractal($practitioner, new UserTransformer())->respond();
    }

    public function destroy(User $practitioner, PractitionerDestroyRequest $request)
    {
        $practitioner->delete();
        event(new AccountDeleted($practitioner));
        return response(null, 204);
    }

    public function unpublished(User $practitioner)
    {
        $practitioner->forceFill([
            'is_published' => false,
        ]);
        $practitioner->update();
    }

    public function publish(User $practitioner)
    {
        $practitioner->forceFill([
            'is_published' => true,
        ]);
        $practitioner->update();
    }
}
