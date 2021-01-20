<?php

namespace Tests\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Stripe\StripeClient;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use DatabaseTransactions;

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

    protected function createStripeClient(User $user)
    {
        $client = app()->make(StripeClient::class);
        $stripeUser =  $client->customers->create(['email' => $user->email]);
        $this->user->stripe_customer_id = $stripeUser->id;
        $this->user->save();

        return $stripeUser->id;
    }

    protected function createStripePaymentMethod(User $user)
    {
        $stripe        = app()->make(StripeClient::class);
        $paymentMethod = $stripe->paymentMethods->create([
            'type' => 'card',
            'card' => [
                'number'    => '4242424242424242',
                'exp_month' => 1,
                'exp_year'  => 2022,
                'cvc'       => '314',
            ],
        ]);

        $paymentMethodWithCustomer = $stripe->paymentMethods->attach($paymentMethod->id, [
            'customer' => $user->stripe_customer_id
        ]);

        return $paymentMethodWithCustomer;
    }

}
