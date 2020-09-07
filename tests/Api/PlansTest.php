<?php

namespace Tests\Api;


use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Stripe\Service\PlanService;
use Stripe\Service\SubscriptionService;
use Stripe\StripeClient;
use Tests\TestCase;

class PlansTest extends TestCase
{
    //use DatabaseTransactions;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_user_can_subscribe_to_a_plan(): void
    {
        $this->mockStripeSubscriptions();

        $plan = Plan::forceCreate(['price' => 1]);
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

    public function test_user_can_store_to_a_plan(): void
    {
        $this->mockStripeStore();
        $plan = Plan::forceCreate(['price' => 1]);
        $planDB = factory(Plan::class)->create();
        $response = $this->json('post', "/api/plans",[
            'name'  => $planDB->name,
        ]);
        $response->assertOk();
    }

    protected function mockStripeStore()
    {
        $post = new \stdClass();
        $post->id = 12;
        $post->amount = 2000;

        $storeStripePlan = Mockery::mock(PlanService::class, function ($storeStripePlan) use ($post) {
            $storeStripePlan->shouldReceive('create')->andReturn($post);
        });
        $stripe = Mockery::mock(StripeClient::class, function ($stripe) use ($storeStripePlan) {
            $stripe->plans = $storeStripePlan;
        });
        $this->instance(StripeClient::class, $stripe);
    }


}
//return $this->instance(Class::class, Mockery::mock(Class::class, function ($mock) {
//            $mock->shouldReceive('execute')
//                ->andReturn(1);
//        }));
