<?php

namespace Tests\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Stripe\Service\CustomerService;
use Stripe\StripeClient;
use Mockery;
use Tests\TestCase;

class CardStripeTest extends TestCase
{
    use DatabaseTransactions;


    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }
    public function test_get_all_card_client(): void
    {
        $this->mockStripeGetAllCard();
        $response = $this->json('get', "/api/credit-cards");
        $response->assertOk();
    }
    public function test_store_card_client(): void
    {
        $this->mockStripeStoreCard();
        $response = $this->json('post', "/api/credit-cards");
        $response->assertOk();
    }

    protected function mockStripeStoreCard()
    {
        $storeCard = Mockery::mock(CustomerService::class, function ($storeCard)  {
            $storeCard->shouldReceive('createSource');
        });
        $stripe = Mockery::mock(StripeClient::class, function ($stripe) use ($storeCard) {
            $stripe->customers = $storeCard;
        });
        $this->instance(StripeClient::class, $stripe);
    }
    protected function mockStripeGetAllCard()
    {
        $allCard = Mockery::mock(CustomerService::class, function ($allCard)  {
            $allCard->shouldReceive('allSources');
        });
        $stripe = Mockery::mock(StripeClient::class, function ($stripe) use ($allCard) {
            $stripe->customers = $allCard;
        });
        $this->instance(StripeClient::class, $stripe);
    }
}
