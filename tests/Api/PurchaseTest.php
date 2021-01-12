<?php


namespace Tests\Api;

use App\Models\Purchase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_user_can_see_purchase_list(): void
    {
        Purchase::factory()->count(2)->create();

        $response = $this->actingAs($this->user)->json('get','/api/purchases');
        $response->assertOk();
    }
}
