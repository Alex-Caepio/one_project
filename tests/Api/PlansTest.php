<?php

namespace Tests\Api;


use App\Models\Plan;
use App\Models\User;
use App\Traits\StripeTesting;
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
    use StripeTesting;


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
        $paymentMethod = $this->createStripePaymentMethod( $this->user,'4242424242424242',);

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
}
