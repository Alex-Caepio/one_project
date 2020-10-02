<?php

namespace Tests\Admin;

use App\Models\Plan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;
use Stripe\Service\PlanService;
use Stripe\StripeClient;
use Tests\TestCase;

class PlanTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->login($this->user);
    }

    public function test_all_plan(): void
    {
        Plan::factory()->count(2)->create();
        $response = $this->json('get', "/admin/plans");

        $response
            ->assertOk()->assertJsonStructure([
                ['id', 'name'],
            ]);
    }

    public function test_user_can_store_to_a_plan(): void
    {
        $this->mockStripeStore();
        $planDB = Plan::factory()->create(['price' => 1]);
        $response = $this->json('post', "/admin/plans", [
            'name' => $planDB->name,
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

    public function test_show_plan(): void
    {
        $plan = Plan::factory()->create();
        $response = $this->json('get', "/admin/plans/{$plan->id}");

        $response->assertOk();
    }

    public function test_update_plan(): void
    {
        $plan = Plan::factory()->create();
        $response = $this->json('put', "admin/plans/{$plan->id}",
            [
                'name' => $plan->name,
            ]);

        $response->assertOk();
    }

    public function test_delete_plan(): void
    {
        $plan = Plan::factory()->create();
        $response = $this->json('delete', "/admin/plans/{$plan->id}");

        $response->assertStatus(204);
    }
}
