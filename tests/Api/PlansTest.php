<?php

namespace Tests\Api;


use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Mockery;
use Stripe\Service\PlanService;
use Stripe\Service\SubscriptionService;
use Stripe\StripeClient;
use Tests\TestCase;

class PlansTest extends TestCase
{
    use DatabaseTransactions;


    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }
    public function test_get_service_types_plan(): void
    {

        Plan::factory()->create();
        $response = $this->json('get', "/api/plans");
        $response->assertOk();
    }

    public function test_user_can_subscribe_to_a_plan(): void
    {
        Event::fake();
        $stripeProduct = $this->creteStripeProduct();
        $stripePrirce  = $this->creteStripeRecurringPrice($stripeProduct);
        $stripeUser    = $this->createStripeClient($this->user);
        $paymentMethod = $this->createStripePaymentMethod('4242424242424242', $this->user);

        $plan     = Plan::factory()->create(['price' => 1, 'stripe_id' => $stripePrirce->id]);
        $payload  = [
            'payment_method_id' => $paymentMethod->id
        ];
        $response = $this->json('post', "/api/plans/{$plan->id}/purchase", $payload);
        $response->assertStatus(204);
    }

    protected function mockStripeSubscriptions()
    {
        $subscription = new \stdClass();
        $subscription->id = 12;
        $subscription->current_period_end = '2020-12-20 12:20:12';

        $subscriptionService = Mockery::mock(SubscriptionService::class, function ($subscriptionService) use ($subscription) {
            $subscriptionService->shouldReceive('create')->andReturn($subscription);
        });

        $stripe = Mockery::mock(StripeClient::class, function ($stripe) use ($subscriptionService) {
            $stripe->subscriptions = $subscriptionService;
        });

        $this->instance(StripeClient::class, $stripe);
    }

    protected function creteStripeProduct()
    {
        $client = app()->make(StripeClient::class);
        return $client->products->create(['name' => 'Test product @' . now()->toDateTimeString()]);
    }

    protected function creteStripeRecurringPrice($product)
    {
        $client = app()->make(StripeClient::class);
        return $client->prices->create([
            'unit_amount' => '1000',
            'currency'    => 'usd',
            'product'     => $product,
            'recurring' => ['interval' => 'month'],
        ]);
    }

    protected function createStripeClient(User $user)
    {
        $client = app()->make(StripeClient::class);
        $stripeUser =  $client->customers->create(['email' => $user->email]);
        $this->user->stripe_customer_id = $stripeUser->id;
        $this->user->save();

        return $stripeUser;
    }

    protected function createStripePaymentMethod($cardNumber, User $user)
    {
        $client        = app()->make(StripeClient::class);
        $paymentMethod = $client->paymentMethods->create([
            'type' => 'card',
            'card' => [
                'number'    => $cardNumber,
                'exp_month' => 1,
                'exp_year'  => 2022,
                'cvc'       => '314',
            ],
        ]);

        $client->paymentMethods->attach($paymentMethod->id, [
            'customer' => $user->stripe_customer_id
        ]);

        return $paymentMethod;
    }

}
