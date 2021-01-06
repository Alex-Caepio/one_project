<?php

namespace Tests\Admin;

use App\Models\Promotion;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PromotionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->login($this->user);
    }

    public function test_admin_can_see_promotion_list(): void
    {
        Promotion::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->json('get','/admin/promotions');
        $response->assertOk();
    }

    public function test_admin_can_see_promotion(): void
    {
        $promotion = Promotion::factory()->create();

        $response = $this->actingAs($this->user)->json('get',"/admin/promotions/{$promotion->id}");
        $response->assertOk();
    }

    public function test_admin_can_store_promotions(): void
    {
        $response = $this->actingAs($this->user)->json('post',"/admin/promotions",[
            'name' => 'promoname',
            'discount_type' => 'percentage',
            'discount_value' => 5,
            'applied_to' => 'host',
            'total_codes' => 1
        ]);
        $response->assertOk();
    }
    public function test_admin_can_update_promotions(): void
    {
        $promotion = Promotion::factory()->create();

        $response = $this->actingAs($this->user)->json('put',"/admin/promotions/{$promotion->id}",[
            'name' => 'promoname',
            'discount_type' => 'percentage',
            'discount_value' => 5,
            'applied_to' => 'host',
            'total_codes' => 1
        ]);
        $response->assertOk();
        $response->assertJson([
            'name' => 'promoname'
        ]);
    }

    public function test_admin_can_delete_promotion(): void
    {
        $promotion = Promotion::factory()->create();

        $response = $this->actingAs($this->user)->json('delete',"/admin/promotions/{$promotion->id}");
        $response->assertStatus(204);
    }

    public function test_admin_can_change_promotion_status(): void
    {
        $promotion_active = Promotion::factory()->create(['status' => 'active']);

        $response = $this->actingAs($this->user)->json('post',
            "/admin/promotions/{$promotion_active->id}/disable");
        $response->assertOk()
            ->assertJson(['status' => 'disabled']);

        $promotion_disabled = Promotion::factory()->create([
            'status' => 'disabled',
            'expiry_date' => '2022-12-12'
            ]);

        $response = $this->actingAs($this->user)->json('post',
            "/admin/promotions/{$promotion_disabled->id}/enable");
        $response->assertOk()
            ->assertJson(['status' => 'active']);
    }
}
