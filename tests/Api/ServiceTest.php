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
        $user = User::factory()->create(['account_type' => 'practitioner']);
        $service = Service::factory()->make(['user_id' => $user->id]);
        $type    = ServiceType::factory()->create();

        $response = $this->actingAs($user)->json('post', '/api/services', [
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
        $this->user->user_type = 'practitioner';
        /** @var Service $service */
        $user = User::factory()->create(['account_type' => 'practitioner']);
        $service_type = ServiceType::factory()->create();
        $service = Service::factory()->make(['user_id' => $user->id, 'service_type_id' => $service_type->id]);

        $response = $this->actingAs($user)->json('post', '/api/services', [
            'url'          => $service->url,
            'title'        => $service->title,
            'user_id'      => $service->user_id,
            'description'  => $service->description,
            'introduction' => $service->introduction,
            'is_published' => $service->is_published,
            'service_type_id' => $service_type->id,
            'media_images' => [
                ['url' => 'http://google.com'],
                ['url' => 'http://google.com'],
            ]
        ]);
        $response->assertOk();
        $this->assertCount(2, Service::first()->media_images);
    }

    public function test_user_update_service(): void
    {
        $user = User::factory()->create(['account_type' => 'practitioner']);
        $service_type = ServiceType::factory()->create();
        $service    = Service::factory()->create(['user_id' => $user->id,'service_type_id' => $service_type->id]);
        $newService = Service::factory()->make();
        $payload    = [
            'title'        => $newService->title,
            'keyword_id'   => $newService->keyword_id,
            'user_id'      => $user->id,
            'description'  => $newService->description,
            'is_published' => true,
            'introduction' => $newService->introduction,
            'service_type_id' => $service->service_type_id,
            'url'          => $newService->url,
        ];
        $response   = $this->actingAs($user)->json('put', "/api/services/{$service->id}", $payload);

        $response->assertOk();
    }

    public function test_practitioner_can_delete_service(): void
    {
        $user = User::factory()->create(['account_type' => 'practitioner']);

        $service  = Service::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->json('delete', "/api/services/{$service->id}");

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
        $user = User::factory()->create(['account_type' => 'practitioner']);
        $service_type = ServiceType::factory()->create();
        $service    = Service::factory()->create(['user_id' => $user->id, 'service_type_id' => $service_type->id]);
        $newService = Service::factory()->make();

        $response = $this->actingAs($user)->json('put', "/api/services/{$service->id}",
            [
                'title' => $newService->title,
                'service_type_id' => $service_type->id
            ]);

        $response->assertOk()
            ->assertJson([
                'title' => $newService->title,
            ]);
    }

    public function test_service_can_be_created_with_keyword()
    {
        $user = User::factory()->create(['account_type' => 'practitioner']);
        $service_type = ServiceType::factory()->create();
        $service  = Service::factory()->make(['user_id' => $user->id, 'service_type_id' => $service_type->id]);

        $response = $this->actingAs($user)->json('post', '/api/services', [
            'url'          => $service->url,
            'title'        => $service->title,
            'user_id'      => $service->user_id,
            'service_type_id' => $service_type->id,
            'keywords'      => [
                'Meditation',
                'Relaxation',
            ],
        ]);

        $response->assertOk();
        $this->assertCount(2, Service::first()->keywords);
    }

    public function test_service_can_be_updated_with_keywords()
    {
        $user = User::factory()->create(['account_type' => 'practitioner']);
        $service_type = ServiceType::factory()->create();
        $service    = Service::factory()->create(['user_id' => $user->id,'service_type_id' => $service_type->id]);
        $keyword    = Keyword::factory()->count(2)->create();

        $response = $this->actingAs($user)->json('put', "/api/services/{$service->id}", [
            'url'          => $service->url,
            'title'        => $service->title,
            'user_id'      => $service->user_id,
            'keyword_id'   => $keyword->pluck('id'),
            'service_type_id' => $service_type->id
        ]);

        $response->assertOk();
        $this->assertCount(2, Service::first()->keywords);
    }

    public function test_keyword_exist_and_has_relation()
    {
        $user = User::factory()->create(['account_type' => 'practitioner']);
        $service_type = ServiceType::factory()->create();
        $keyword    = Keyword::factory()->create();
        $service    = Service::factory()->create(
            [
                'user_id'     => $user->id,
                'keyword_id'  => $keyword->id,
                'service_type_id' => $service_type->id
            ]
        );

        $response = $this->actingAs($user)->json('post', '/api/services', [
            'url'          => $service->url,
            'title'        => $service->title,
            'user_id'      => $service->user_id,
            'service_type_id' => $service_type->id
        ]);

        $response->assertOk();
        $this->assertCount(1, Service::first()->keywords);
    }

    public function test_keywords_can_be_unrelated_from_service()
    {
        $user = User::factory()->create(['account_type' => 'practitioner']);
        $service_type = ServiceType::factory()->create();
        $service    = Service::factory()->create([
            'user_id' => $user->id,
            'service_type_id' => $service_type->id,
            'keyword_id' => 1
        ]);

        $response = $this->actingAs($user)->json('put', "/api/services/{$service->id}", [
            'url'          => $service->url,
            'title'        => $service->title,
            'user_id'      => $service->user_id,
            'service_type_id' => $service_type->id,
            'keywords'     => [],
        ]);

        $response->assertOk();
        $this->assertCount(0, Service::first()->keywords);
    }
}
