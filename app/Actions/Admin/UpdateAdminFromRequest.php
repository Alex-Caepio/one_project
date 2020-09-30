<?php


namespace App\Actions\Admin;


use App\Events\PasswordChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Stripe\StripeClient;

class UpdateAdminFromRequest
{
    public function execute(Request $request)
    {
        $profile = Auth::user();
        if (Hash::make($request->get('password'))) {
            $stripe = app(StripeClient::class);
            if ($profile->stripe_id == $stripe) {
                 $stripe->customers->update(
                    $profile->stripe_id,
                    ['email' => $request->get('email'),]);
            }
            $profile->forceFill([
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
            ]);
            $profile->update();
        }
    }
}
