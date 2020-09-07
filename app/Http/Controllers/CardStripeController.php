<?php

namespace App\Http\Controllers;

use App\Actions\User\AllCardUser;
use App\Actions\User\StoreCardUser;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class CardStripeController extends Controller
{
    public function index(StripeClient $stripeClient)
    {
        $allCardsUser = run_action(AllCardUser::class, $stripeClient);
        return response($allCardsUser);
    }

    public function store()
    {
        run_action(StoreCardUser::class);
    }

}
