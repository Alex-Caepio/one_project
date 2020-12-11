<?php

namespace Tests\Api;

use App\Models\User;
use App\Models\Service;
use App\Models\Keyword;
use App\Models\ServiceType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use DatabaseTransactions, WithoutMiddleWare;

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
        $type    = ServiceType::factory()->create();

        $response = $this->json('post', '/api/services', [
            'description'     => $service->description,
            'service_type_id' => $type->id,
            'introduction'    => $service->introduction,
            'is_published'    => $service->is_published,
            'title'           => $service->title,
            'user_id'         => $service->user_id,
            'url'             => $service->url,
            'keywords'        => ['waka']
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
        $this->assertCount(2, $service->media_images);
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

    public function test_service_can_be_created_with_keyword()
    {
        $service  = Service::factory()->create();

        $response = $this->json('post', '/api/services', [
            'url'          => $service->url,
            'title'        => $service->title,
            'user_id'      => $service->user_id,
            'keywords'      => [
                ['title' => 'Meditation'],
                ['title' => 'Relaxation'],
            ],
        ]);

        $response->assertOk();
        $this->assertCount(2, $service->keywords);
    }

    public function test_service_can_be_updated_with_keywords()
    {
        $service    = Service::factory()->create();
        $keyword    = Keyword::factory()->count(2)->create();

        $response = $this->json('put', "/api/services/{$service->id}", [
            'url'          => $service->url,
            'title'        => $service->title,
            'user_id'      => $service->user_id,
            'keyword_id'   => $keyword->pluck('id'),
        ]);

        $response->assertOk();
        $this->assertCount(2, $service->keywords);
    }

    public function test_keyword_exist_and_has_relation()
    {
        $keyword    = Keyword::factory()->create();
        $service    = Service::factory()->create(
            ['keyword_id'  => $keyword->id]
        );

        $response = $this->json('post', '/api/services', [
            'url'          => $service->url,
            'title'        => $service->title,
            'user_id'      => $service->user_id,
        ]);

        $response->assertOk();
        $this->assertCount(1, $service->keywords);
    }

    public function test_keywords_can_be_unrelated_from_service()
    {
        $service    = Service::factory()->create(['keyword_id' => 1]);

        $response = $this->json('put', "/api/services/{$service->id}", [
            'url'          => $service->url,
            'title'        => $service->title,
            'user_id'      => $service->user_id,
            'keywords'     => [],
        ]);

        $response->assertOk();
        $this->assertCount(0, $service->keywords);
    }
}
