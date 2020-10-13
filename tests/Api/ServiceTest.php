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
        Service::factory()->count(2)->create();
        $response = $this->json('get', "/api/services");
        $response
            ->assertOk()
            ->assertJsonStructure([
                ['id', 'title'],
            ]);
    }

    public function test_practitioner_can_create_service(): void
    {
        $service = Service::factory()->make();

        $response = $this->json('post', '/api/services', [
            'description'  => $service->description,
            'introduction' => $service->introduction,
            'is_published' => $service->is_published,
            'title'        => $service->title,
            'user_id'      => $service->user_id,
            'url'          => $service->url,
        ]);
        $response->assertOk();
    }

    public function test_practitioner_can_create_service_with_image_media_links(): void
    {
        /** @var Service $service */
        $service = Service::factory()->make();

        $response = $this->json('post', '/api/services', [
            'url'          => $service->url,
            'title'        => $service->title,
            'user_id'      => $service->user_id,
            'description'  => $service->description,
            'introduction' => $service->introduction,
            'is_published' => $service->is_published,
            'media_images' => [
                ['url' => 'http://google.com'],
                ['url' => 'http://google.com'],
            ]
        ]);
        $response->assertOk();
        $this->assertCount(2, $service->mediaImages);
    }

    public function test_user_update_service(): void
    {
        $service    = Service::factory()->create();
        $newService = Service::factory()->make();
        $payload    = [
            'title'        => $newService->title,
            'keyword_id'   => $newService->keyword_id,
            'user_id'      => $newService->user_id,
            'description'  => $newService->description,
            'is_published' => $newService->is_published,
            'introduction' => $newService->introduction,
            'url'          => $newService->url,
        ];
        $response   = $this->json('put', "/api/services/{$service->id}", $payload);

        $response->assertOk();
    }

    public function test_practitioner_can_delete_service(): void
    {
        $service  = Service::factory()->create();
        $response = $this->json('delete', "/api/services/{$service->id}");

        $response->assertStatus(204);
    }

    public function test_store_service_favorite(): void
    {
        $authUser  = User::factory()->create();
        $serviceId = Service::factory()->create();
        $response  = $this->json('post', "services/{$serviceId->id}/favourite");
        $authUser->favourite_services()->attach($serviceId);

        $this->assertDatabaseHas('favorites', [
            'user_id'    => $authUser->id,
            'service_id' => $serviceId->id
        ]);
    }

    public function test_practitioner_can_update_service(): void
    {
        $service    = Service::factory()->create();
        $newService = Service::factory()->make();

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
