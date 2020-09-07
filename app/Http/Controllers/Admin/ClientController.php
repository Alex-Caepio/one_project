<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CreateAdminFromRequest;
use App\Actions\Stripe\CreateStripeUserByEmail;
use App\Events\ClientAccount;
use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ClientDestroyRequest;
use App\Http\Requests\Admin\ClientShowRequest;
use App\Http\Requests\Admin\ClientUpdateRequest;
use App\Http\Requests\AdminUpdateRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Request;
use App\Listeners\AccountDeleted;
use App\Mail\VerifyEmail;
use App\Models\User;
use App\Transformers\UserTransformer;
use Hash;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Stripe\StripeClient;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $paginator = User::where('account_type', 'user')->paginate($request->getLimit());
        $user = $paginator->getCollection();
        return response(fractal($user, new UserTransformer())->parseIncludes($request->getIncludes()))
            ->withPaginationHeaders($paginator);
    }

    public function store(RegisterRequest $request)
    {
        $customer = run_action(CreateStripeUserByEmail::class, $request->email);
        $user = run_action(CreateAdminFromRequest::class, $request, ['stripe_id' => $customer->id, 'is_admin' => null, 'account_type' => 'user']);

        $token = $user->createToken('access-token');
        $user->withAccessToken($token);

        event(new UserRegistered($user));

        return fractal($user, new UserTransformer())
            ->parseIncludes('access_token')
            ->respond();
    }

    public function show(User $admin, ClientShowRequest $request)
    {
        return fractal($admin, new UserTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function update(ClientUpdateRequest $request, User $admin)
    {
        $admin->update($request->all());
        return fractal($admin, new UserTransformer())->respond();
    }

    public function destroy(User $client, ClientDestroyRequest $request)
    {
        $client->delete();
        //Event::fire(new AccountDeleted());
        event(new ClientAccount($client));
        return response(null, 204);
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
}
