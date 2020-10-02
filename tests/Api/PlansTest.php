<?php

namespace Tests\Api;


use App\Models\Plan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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

    public function test_user_can_subscribe_to_a_plan(): void
    {
        $this->mockStripeSubscriptions();

        $plan = Plan::factory()->create(['price' => 1]);
        $response = $this->json('post', "/api/plans/{$plan->id}/purchase");
        $response->assertOk();
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



}
