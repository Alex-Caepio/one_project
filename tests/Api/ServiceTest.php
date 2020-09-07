<?php

namespace Tests\Api;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_user_can_see_services_list(): void
    {
        factory(Service::class, 2)->create();
        $response = $this->json('get', "/api/services");
        $response
            ->assertOk()
            ->assertJsonStructure([
                ['id', 'title'],
            ]);
    }

    public function test_practitioner_can_create_service(): void
    {
        $service = factory(Service::class)->make();

        $response = $this->json('post', '/api/services', [
            'description'  => $service->description,
            'introduction' => $service->introduction,
            'is_published' => $service->is_published,
            'title'        => $service->title,
            'user_id'        => $service->user_id,
            'url'          => $service->url,
        ]);
        $response->assertOk();
    }

    public function test_practitioner_can_delete_service(): void
    {
        $service = factory(Service::class)->create();
        $response = $this->json('delete', "/api/services/{$service->id}");

        $response->assertStatus(204);
    }
    public function test_store_service_favorite(): void
    {
        $authUser = factory(User::class)->create();
        $serviceId = factory(Service::class)->create();
        $response = $this->json('post', "services/{$serviceId->id}/favourite");
        $authUser->favourite_services()->attach($serviceId);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $authUser->id,
            'service_id' => $serviceId->id
        ]);
    }

    public function test_practitioner_can_update_service(): void
    {
        $service = factory(Service::class)->create();
        $newService = factory(Service::class)->make();

        $response = $this->json('put', "/api/services/{$service->id}",
            [
                'title' => $newService->title,
            ]);

        $response->assertOk()
            ->assertJson([
                'title' => $newService->title,
            ]);
    }
}
