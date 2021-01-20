<?php

namespace Tests\Api;

use App\Models\User;
use App\Traits\usesStripe;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Stripe\StripeClient;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use DatabaseTransactions;
    use usesStripe;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    function test_get_payment_methods(): void
    {
        $stripeUser = $this->createStripeClient($this->user);
        $this->createStripePaymentMethod($this->user);

        $response = $this->json('get', "/api/payment-methods", ['customer' => $stripeUser]);
        $response->assertOk();
    }

    public function test_store_payment_methods(): void
    {
        $stripeUser = $this->createStripeClient($this->user);
        $paymentMethod = $this->createStripePaymentMethod($this->user);

        $response = $this->json('post', "/api/payment-methods",[
            'payment_method_id' => $paymentMethod->id,
            'customer' => $stripeUser
        ]);
        $response->assertStatus(200);
    }
}
