<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Stripe\CreateStripeUserByEmail;
use App\Actions\User\CreateUserFromRequest;
use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Transformers\UserTransformer;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $customer = run_action(CreateStripeUserByEmail::class, $request->email);
        $user = run_action(CreateUserFromRequest::class, $request, ['stripe_id' => $customer->id]);

        $token = $user->createToken('access-token');
        $user->withAccessToken($token);

        event(new UserRegistered($user));

        return fractal($user, new UserTransformer())
            ->parseIncludes('access_token')
            ->respond();
    }


    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->get('email'))->first();
        if (!$user) {
            $this->invalidate(); //user not exists
        }

        if (!Hash::check($request->get('password'), $user->password)) {
            $this->invalidate(); //invalid password
        }

        $user->withAccessToken($user->createToken('access-token'));

        return fractal($user, new UserTransformer())
            ->parseIncludes('access_token')
            ->respond();
    }

    protected function invalidate()
    {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect']
        ]);
    }

    public function profile(Request $request)
    {
        return fractal($request->user(), new UserTransformer())
            ->respond();
    }

    public function update(Request $request)
    {
        Auth::user()->update($request->all());
        $user = Auth::user();
        return fractal($user, new UserTransformer())->respond();
    }

    public function avatar(Request $request)
    {
        $path = public_path('\img\profile\\' . Auth::id() . '\\');
        $fileName = $request->file('image')->getClientOriginalName();
        $request->file('avatar')->move($path, $fileName);
    }

    public function background(Request $request)
    {
        $path = public_path('\img\profile\\' . Auth::id() . '\\');
        $fileName = $request->file('image')->getClientOriginalName();
        $request->file('background')->move($path, $fileName);
    }

    public function verifyEmail(Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        $user = User::where('id', $request->user)
            ->where('email', $request->email)
            ->first();

        $user->email_verified_at = now();
        $user->save();
        response(null, 200);
    }

    public function resendVerification(Request $request)
    {
        $this->sendVerificationEmail($request->user());
        response(null, 200);
    }

}
