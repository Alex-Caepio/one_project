<?php

namespace App\Http\Controllers\Auth;

use App\Events\PasswordChanged;
use App\Models\PasswordReset;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;
use App\Http\Requests\Auth\ResetPasswordAsk;
use App\Http\Requests\Auth\ResetPasswordClaim;
use DB;
use Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Events\PasswordReset as ResetEvent;

class ResetPasswordController extends Controller {

    public function askForReset(ResetPasswordAsk $request) {
        $email = strtolower($request->email);

        $token = hash('md5', Str::random(60));

        PasswordReset::where('email', $email)->delete();
        $resetModel = PasswordReset::create(['email' => $email, 'token' => $token]);
        $resetModel->load('user');

        event(new ResetEvent($resetModel));

        return response(null, 200);
    }

    /**
     * Save new password.
     *
     * @param ResetPasswordClaim $request
     * @return \Illuminate\Http\Response
     */
    public function claimReset(ResetPasswordClaim $request) {
        if (!$request->token) {
            return response(null, 500);
        }
        $resetModel = PasswordReset::where('token', $request->token)->with('user')->first();
        $user = $resetModel->user;
        $user->update(['password' => Hash::make($request->password)]);
        $resetModel->delete();

        event(new PasswordChanged($user));

        $user->withAccessToken($user->createToken('access-token'));

        return fractal($user, new UserTransformer())->parseIncludes('access_token')->respond();
    }

    public function verifyToken(Request $request) {
        $validToken = PasswordReset::where('token', $request->token)->with('user')->first();
        if (!$validToken) {
            return response(null, 422);
        }

        $expiresAd = Carbon::parse($validToken->created_at)->addHours(48);
        if ($expiresAd > Carbon::now()) {
            return response(null, 200);
        }

        return response(null, 422);
    }
}
