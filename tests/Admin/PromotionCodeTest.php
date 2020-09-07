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
        factory(PromotionCode::class, 2)->create();
        $response = $this->json('get', "/admin/promotion-code");

        $response->assertOk();
    }
    public function test_store_promotion(): void
    {
        $promotion = factory(PromotionCode::class)->create();
        $response = $this->json('post', '/admin/promotion-code', [
            'name' => $promotion->name,
            'valid_from' => $promotion->valid_from,
            'expiry_date' => $promotion->expiry_date,
            'discount_type' => $promotion->discount_type,
            'discount_value' => $promotion->discount_value,
            'service_type_id' => $promotion->service_type_id,
            'discipline_id' => $promotion->discipline_id,
            'focus_area_id' => $promotion->focus_area_id,
            'max_uses_per_code' => $promotion->max_uses_per_code,
            'code_uses_per_customer' => $promotion->code_uses_per_customer,
        ]);
        $response->assertOk($promotion);
    }
    public function test_update_promotion(): void
    {
        $promotion = factory(PromotionCode::class)->create();
        $newPromotion = factory(PromotionCode::class)->make();

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
        $promotion = factory(PromotionCode::class)->create();
        $response = $this->json('delete', "/admin/promotion-code/{$promotion->id}/destroy");

        $response->assertStatus(204);
    }
}
