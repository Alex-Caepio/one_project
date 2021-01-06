<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Stripe\CreateStripeUserByEmail;
use App\Actions\User\CreateUserFromRequest;
use App\Events\PasswordChanged;
use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\PublishRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateRequest;
use App\Models\User;
use App\Transformers\UserTransformer;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Stripe\StripeClient;


class AuthController extends Controller
{
    public function register(RegisterRequest $request, StripeClient $stripe)
    {
        $stripeCustomer = run_action(CreateStripeUserByEmail::class, $request->email);
        $stripeAccount  = $stripe->accounts->create([
            'type'  => 'standard',
            'email' => $request->email,
        ]);
        $user           = run_action(CreateUserFromRequest::class, $request, [
            'stripe_customer_id' => $stripeCustomer->id,
            'stripe_account_id'  => $stripeAccount->id
        ]);

        event(new UserRegistered($user));

        return fractal($user, new UserTransformer())
            ->respond();
    }


    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->get('email'))->first();
        $user->withAccessToken($user->createToken('access-token'));

        return fractal($user, new UserTransformer())
            ->parseIncludes('access_token')
            ->respond();
    }

    public function publish(PublishRequest $request)
    {
        $user               = $request->user();
        $user->is_published = true;
        $user->save();
        return fractal($request->user(), new UserTransformer())
            ->respond();
    }

    public function profile(Request $request)
    {
        return fractal($request->user(), new UserTransformer())
            ->respond();
    }

    public function update(UpdateRequest $request)
    {
        $user = $request->user();
        $user->update($request->all());
        if ($request->filled('password')) {
            $user->password = Hash::make($request->get('password'));
            $user->save();
            event(new PasswordChanged($user));
        }
        return fractal($user, new UserTransformer())->respond();
    }

    public function avatar(Request $request)
    {
        $path     = public_path('\img\profile\\' . Auth::id() . '\\');
        $fileName = $request->file('image')->getClientOriginalName();
        $request->file('avatar')->move($path, $fileName);
    }

    public function background(Request $request)
    {
        $path     = public_path('\img\profile\\' . Auth::id() . '\\');
        $fileName = $request->file('image')->getClientOriginalName();
        $request->file('background')->move($path, $fileName);
    }

    public function verifyEmail(Request $request)
    {
        if (!$request->hasValidSignature() || !$request->user || !$request->email) {
            abort(401);
        }

        $user = User::where('id', $request->user)
            ->where('email', $request->email)
            ->firstOrFail();

        $user->forceFill(['email_verified_at' => now(), 'status' => User::STATUS_ACTIVE]);
        $user->save();

        $user->withAccessToken($user->createToken('access-token'));

        return fractal($user, new UserTransformer())
            ->parseIncludes('access_token')
            ->respond();
    }

    public function resendVerification(Request $request)
    {
        $this->sendVerificationEmail($request->user());
        response(null, 200);
    }

    protected function invalidate()
    {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect']
        ]);
    }

}
