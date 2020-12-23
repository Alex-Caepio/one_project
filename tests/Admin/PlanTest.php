<?php

namespace Tests\Admin;

use App\Http\Controllers\Admin\PlanController;
use App\Models\Plan;
use App\Models\ServiceType;
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

    /**
     * Test different create plan scenarios
     *
     * @link PlanController::store()
     */
    public function test_admin_can_create_plan(): void
    {
        $this->mockStripeStore();

        /* Admin can store a plan with a price */
        $this->postJson(
            action([PlanController::class, 'store']),
            [
                'name'  => 'Test plan',
                'price' => 20,
            ])
            ->assertOk();

        /* Admin can store a free plan without a price */
        $this->postJson(
            action([PlanController::class, 'store']),
            [
                'name'    => 'Test plan',
                'is_free' => true,
            ])
            ->assertOk();

        /* On storing a non-free plan price is required */
        $this->postJson(
            action([PlanController::class, 'store']),
            [
                'name'    => 'Test plan',
                'is_free' => false,
            ])
            ->assertStatus(422);
    }

    protected function mockStripeStore()
    {
        $post         = new \stdClass();
        $post->id     = 12;
        $post->amount = 2000;

        $storeStripePlan = Mockery::mock(PlanService::class, function ($storeStripePlan) use ($post) {
            $storeStripePlan->shouldReceive('create')->andReturn($post);
        });
        $stripe          = Mockery::mock(StripeClient::class, function ($stripe) use ($storeStripePlan) {
            $stripe->plans = $storeStripePlan;
        });
        $this->instance(StripeClient::class, $stripe);
    }

    public function test_show_plan(): void
    {
        $plan     = Plan::factory()->create();
        $response = $this->json('get', "/admin/plans/{$plan->id}");

        $response->assertOk();
    }

    public function test_update_plan(): void
    {
        $plan     = Plan::factory()->create();
        $serviceType = ServiceType::factory()->create();
        $response = $this->json('put', "admin/plans/{$plan->id}",
            [
                'name' => $plan->name,
                'service_types' => [$serviceType->id]
            ]
        );

        $this->assertCount(1, $plan->service_types);

        $response->assertOk();
    }

    public function test_delete_plan(): void
    {
        $plan     = Plan::factory()->create();
        $response = $this->json('delete', "/admin/plans/{$plan->id}");

        $response->assertStatus(204);
    }
}
