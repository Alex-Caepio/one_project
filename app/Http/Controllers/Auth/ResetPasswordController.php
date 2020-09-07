<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\ResetPasswordAsk;
use App\Http\Requests\Auth\ResetPasswordClaim;
use App\Mail\PasswordHasBeenChanged;
use App\Mail\PasswordResetLink;
use App\Http\Controllers\Controller;
use App\Http\Requests\Password\ResetRequest;

use App\Models\User;
use Carbon\Carbon;
use DB;
use Mail;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /**
     * Request password reset link.
     *
     * @param Request $request
     */
    public function askForReset(ResetPasswordAsk $request)
    {
        $email = strtolower($request->email);

        $user = User::where('email', $email)->first();
        if ($user) {
            $token = hash('md5', Str::random(60));

            DB::table('password_resets')
                ->where('email', $email)
                ->delete();
            DB::table('password_resets')
                ->insert(
                [
                    'email'       => $email,
                    'token'       => $token,
                    'created_at'  => Carbon::now(),
                ]
            );

            $passwordResetLink = config('app.frontend_password_reset_link') . '?token=' . $token;

            Mail::to([
                'email' => $email
            ])->send(new PasswordResetLink($passwordResetLink));
        }

        return response(null, 200);
    }

    /**
     * Save new password.
     *
     * @param Request $request
     */
    public function claimReset(ResetPasswordClaim $request)
    {
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

        return response(null, 204);
    }
}
