<?php


namespace App\Actions\Admin;

use App\Http\Requests\Admin\AdminUpdateRequest;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdateAdminFromRequest
{
    public static $safeFields = ['first_name', 'last_name', 'email', 'password','is_admin','account_type'];

    public function execute(AdminUpdateRequest $request)
    {
        $profile = Auth::user();
        if (Hash::make($request->get('password'))) {
            $stripe = app(StripeClient::class);
            if ($profile->stripe_customer_id == $stripe) {
                $stripe->customers->update(
                    $profile->stripe_customer_id,
                    ['email' => $request->get('email'),]);
            }
            $profile->forceFill([
                'first_name' => $request->get('first_name'),
                'last_name'  => $request->get('last_name'),
                'email'      => $request->get('email'),
                'password'   => Hash::make($request->get('password')),
            ]);
            $profile->update();
        }
    }
}
