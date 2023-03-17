<?php

namespace Tests\Api;

use App\Models\User;
use App\Models\ServiceType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ServiceTypeTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_can_see_service_type(): void
    {
        ServiceType::factory()->count(2)->create();
        $response = $this->json('get', '/api/service-types');
        $response
            ->assertOk()
            ->assertJsonStructure([
                ['id', 'name'],
            ]);
    }
}
