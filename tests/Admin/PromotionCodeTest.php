<?php

namespace Tests\Admin;

use App\Models\PromotionCode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PromotionCodeTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->login($this->user);
    }
    public function test_all_promotion(): void
    {
        PromotionCode::factory()->count(2)->create();
        $response = $this->json('get', "/admin/promotion-code");

        $response->assertOk();
    }
    public function test_store_promotion(): void
    {
        $promotion = PromotionCode::factory()->create();
        $response = $this->json('post', '/admin/promotion-code', [
            'name' => $promotion->name,
            'uses_per_client' => $promotion->uses_per_client,
            'uses_per_code' => $promotion->uses_per_code,
            'promotion_id' => $promotion->promotion_id,
        ]);
        $response->assertOk();
    }
    public function test_update_promotion(): void
    {
        $promotion = PromotionCode::factory()->create();
        $newPromotion = PromotionCode::factory()->make();

        $response = $this->json('put', "admin/promotion-code/{$promotion->id}/update",
            [
                'name' => $newPromotion->name,
            ]);

        $response->assertOk()
            ->assertJson([
                'name' => $newPromotion->name,
            ]);
    }
    public function test_delete_promotion(): void
    {
        $promotion = PromotionCode::factory()->create();
        $response = $this->json('delete', "/admin/promotion-code/{$promotion->id}/destroy");

        $response->assertStatus(204);
    }
}
