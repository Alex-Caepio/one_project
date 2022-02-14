<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\DeleteUser;
use App\Filters\UserFiltrator;
use App\Models\User;
use App\Mail\VerifyEmail;
use App\Http\Requests\Request;
use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;
use App\Actions\User\CreateUserFromRequest;
use App\Http\Requests\Admin\ClientShowRequest;
use App\Actions\Stripe\CreateStripeUserByEmail;
use App\Http\Requests\Admin\ClientCreateRequest;
use App\Http\Requests\Admin\ClientUpdateRequest;
use App\Http\Requests\Admin\ClientDestroyRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $userQuery = User::where('account_type', User::ACCOUNT_CLIENT);
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

    public function store(ClientCreateRequest $request)
    {
        $customer = run_action(CreateStripeUserByEmail::class, $request->email);
        $user = run_action(CreateUserFromRequest::class, $request, [
            'stripe_customer_id' => $customer->id,
            'is_admin' => null,
            'account_type' => User::ACCOUNT_CLIENT
        ]);

        $token = $user->createToken('access-token');
        $user->withAccessToken($token);

        event(new UserRegistered($user));

        return fractal($user, new UserTransformer())->parseIncludes('access_token')->respond();
    }

    public function show(User $client, ClientShowRequest $request)
    {
        return fractal($client, new UserTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function update(ClientUpdateRequest $request, User $client)
    {
        $client->forceFill($request->all());
        $client->save();
        return fractal($client, new UserTransformer())->respond();
    }

    public function destroy(User $client, ClientDestroyRequest $request)
    {
        run_action(DeleteUser::class, $client, $request);
        return response(null, 204);
    }

    protected function sendVerificationEmail($user)
    {
        $linkApi = URL::temporarySignedRoute('verify-email', now()->addMinute(60), [
            'user'  => $user->id,
            'email' => $user->email
        ]);

        $linkFrontend = config('app.frontend_url') . config('app.frontend_password_reset_link') . '?' . explode('?', $linkApi)[1];

        Mail::to([
            'email' => $user->email
        ])->send(new VerifyEmail($linkFrontend));
    }
}
