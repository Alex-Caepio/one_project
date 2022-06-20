<?php


namespace App\Http\Requests\Stripe;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class StripeConnectedRequest extends Request {

    public function authorize() {
        return Auth::user()->isPractitioner() && Auth::user()->stripe_account_id;
    }

    public function rules() {
        return [];
    }
}
