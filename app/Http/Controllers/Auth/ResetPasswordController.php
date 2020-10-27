<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Mail\PasswordResetLink;
use App\Http\Controllers\Controller;
use App\Mail\PasswordHasBeenChanged;
use App\Transformers\UserTransformer;
use App\Http\Requests\Auth\ResetPasswordAsk;
use App\Http\Requests\Password\ResetRequest;
use App\Http\Requests\Auth\ResetPasswordClaim;
use DB;
use Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
    public function askForReset(ResetPasswordAsk $request)
    {
        $email = strtolower($request->email);

        $token             = hash('md5', Str::random(60));
        $frontendUrl       = config('app.frontend_reset_password_form_url');
        $passwordResetLink = "{$frontendUrl}?token={$token}";

        DB::table('password_resets')
            ->where('email', $email)
            ->delete();
        DB::table('password_resets')
            ->insert(
                [
                    'email'      => $email,
                    'token'      => $token,
                    'created_at' => Carbon::now(),
                ]
            );
        Mail::to([
            'email' => $email
        ])->send(new PasswordResetLink($passwordResetLink));

        return response(null, 200);
    }

    /**
     * Save new password.
     *
     * @param ResetPasswordClaim $request
     * @return \Illuminate\Http\Response
     */
    public function claimReset(ResetPasswordClaim $request)
    {
        if (!$request->token) {
            return response(null, 500);
        }

        $resetData = DB::table('password_resets')
            ->where('token', $request->token)
            ->first();

        User::where('email', $resetData->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')
            ->where('email', $resetData->email)
            ->delete();

        Mail::to([
            'email' => $resetData->email
        ])->send(new PasswordHasBeenChanged());

        $user = User::where('email', $resetData->email)->first();
        $user->withAccessToken($user->createToken('access-token'));

        return fractal($user, new UserTransformer())
            ->parseIncludes('access_token')
            ->respond();
    }

    public function verifyToken(Request $request)
    {
        $token        = $request->token;
        $validToken   = DB::table('password_resets')->where('token', $token)->first();
        $createdToken = Carbon::parse($validToken->created_at)->addHours(48);
        if ($createdToken > Carbon::now()) {
            return response(null, 200);
        }

        return response(null, 422);
    }
}
