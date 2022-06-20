<?php

namespace App\Http\Controllers;

use App\Actions\User\StoreCreditCard;
use App\Actions\User\GetCreditCards;
use Stripe\StripeClient;

class CardStripeController extends Controller
{
    public function index(StripeClient $stripeClient)
    {
        return response(run_action(GetCreditCards::class, $stripeClient));
    }

    public function store(StripeClient $stripe)
    {
        run_action(StoreCreditCard::class,$stripe);

        return response(null, 200);
    }

}
