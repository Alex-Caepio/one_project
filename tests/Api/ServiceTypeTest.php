<?php

namespace Tests\Api;

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
        $response = $this->json('get', "/api/service_types");
        $response
            ->assertOk()
            ->assertJsonStructure([
                ['id', 'name'],
            ]);
    }

    public function test_can_create_service_type(): void
    {
        $serviceType = ServiceType::factory()->make();

        $response = $this->json('post', '/api/service_types', [
            'name'  => $serviceType->name,
        ]);
        $response->assertOk();

    }
    public function test_can_get_all_service_type_list(): void
    {
        $serviceType= ServiceType::factory()->count(2)->create();
        $response = $this->json('get', "/api/service_types/list");
        $response->assertOk($serviceType);
    }
}
