<?php

namespace Tests\Api;

use App\Models\ServiceType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StripeAccountTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_getting_users_stripe_account(): void
    {
        ServiceType::factory()->count(2)->create();
        $response = $this->json('get', "/api/service-types");
        $response
            ->assertOk()
            ->assertJsonStructure([
                ['id', 'name'],
            ]);
    }

    public function test_getting_users_stripe_onboard_link(): void
    {
        ServiceType::factory()->count(2)->create();
        $response = $this->json('get', "/api/service-types");
        $response
            ->assertOk()
            ->assertJsonStructure([
                ['id', 'name'],
            ]);
    }

}
