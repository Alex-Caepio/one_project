<?php

namespace Tests\Api;

use App\Models\User;
use App\Models\Service;
use App\Models\Keyword;
use App\Models\ServiceType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Stripe\StripeClient;
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
        Service::factory()->count(10)->create(['is_published' => true]);
        $response = $this->json('get', "/api/services");
        $response
            ->assertOk()
            ->assertJsonCount(10)
            ->assertJsonStructure([
                ['id', 'title'],
            ]);
    }

    public function test_practitioner_can_create_service(): void
    {
        $user = User::factory()->create(['account_type' => 'practitioner']);
        $service = Service::factory()->make(['user_id' => $user->id]);
        $serviceType = ServiceType::factory()->create();

        $response = $this->actingAs($user)->json('post', '/api/services', [
            'description'     => $service->description,
            'service_type_id' => $serviceType->id,
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
        $serviceType = ServiceType::factory()->create();
        $service = Service::factory()->make(['user_id' => $user->id, 'service_type_id' => $serviceType->id]);

        $response = $this->actingAs($user)->json('post', '/api/services', [
            'url'          => $service->url,
            'title'        => $service->title,
            'user_id'      => $service->user_id,
            'description'  => $service->description,
            'introduction' => $service->introduction,
            'is_published' => $service->is_published,
            'service_type_id' => $serviceType->id,
            'media_images'           => [
                'http://google.com',
                'http://google.com',
            ],
            'media_videos'           => [
                [
                    'url' => 'http://google.com',
                    'preview' => 'http://google.com',
                ],

                [
                    'url' => 'http://yandex.com',
                    'preview' => 'http://facebook.com',
                ],
            ],
        ]);
        $response->assertOk();
        $this->assertCount(2, Service::first()->media_images);
        $this->assertCount(2, Service::first()->media_videos);
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
        $this->json('post', "services/{$serviceId->id}/favourite");
        $authUser->favourite_services()->attach($serviceId);

        $this->assertDatabaseHas('favorites', [
            'user_id'    => $authUser->id,
            'service_id' => $serviceId->id
        ]);
    }

    public function test_practitioner_can_update_service(): void
    {
        $user = User::factory()->create(['account_type' => 'practitioner']);
        $serviceType = ServiceType::factory()->create();
        $stripeProduct = $this->creteStripeProduct();
        $service       = Service::factory()->create([
            'user_id'         => $user->id,
            'service_type_id' => $serviceType->id,
            'stripe_id'       => $stripeProduct->id
        ]);
        $newService = Service::factory()->make();

        $response = $this->actingAs($user)->json('put', "/api/services/{$service->id}",
            [
                'title' => $newService->title,
                'service_type_id' => $serviceType->id,
                'introduction' => $service->introduction
            ]);

        $response->assertOk()
            ->assertJson([
                'title' => $newService->title,
            ]);
    }

    public function test_service_can_be_created_with_keyword()
    {
        $user = User::factory()->create(['account_type' => 'practitioner']);
        $serviceType = ServiceType::factory()->create();
        $service  = Service::factory()->make(['user_id' => $user->id, 'service_type_id' => $serviceType->id]);

        $response = $this->actingAs($user)->json('post', '/api/services', [
            'url'          => $service->url,
            'title'        => $service->title,
            'user_id'      => $service->user_id,
            'service_type_id' => $serviceType->id,
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
        $serviceType = ServiceType::factory()->create();
        $keyword = Keyword::factory()->create();
        $service    = Service::factory()->create([
            'user_id'         => $user->id,
            'service_type_id' => $serviceType->id,
            'keyword_id'        => $keyword->id,
        ]);

        $response = $this->actingAs($user)->json('put', "/api/services/{$service->id}", [
            'title'           => $service->title,
            'user_id'         => $service->user_id,
            'service_type_id' => $serviceType->id,
            'introduction'    => $service->introduction,
            'keywords'        => [
                'Yoga',
            ]
        ]);
        $response->assertOk();

    }

    public function test_keywords_can_be_unrelated_from_service()
    {
        $user = User::factory()->create(['account_type' => 'practitioner']);
        $serviceType = ServiceType::factory()->create();
        $service    = Service::factory()->create([
            'user_id' => $user->id,
            'service_type_id' => $serviceType->id,
            'keyword_id' => 1
        ]);

        $response = $this->actingAs($user)->json('put', "/api/services/{$service->id}", [
            'title'        => $service->title,
            'user_id'      => $service->user_id,
            'service_type_id' => $serviceType->id,
            'introduction' => $service->introduction,
            'keywords'     => [],
        ]);

        $response->assertOk();
        $this->assertCount(0, Service::first()->keywords);
    }

    public function test_service_can_be_updated_with_media()
    {
        $user = User::factory()->create(['account_type' => 'practitioner']);
        $serviceType = ServiceType::factory()->create();
        $service    = Service::factory()->create([
            'user_id'         => $user->id,
            'service_type_id' => $serviceType->id,
        ]);

        $response = $this->actingAs($user)->json('put', "/api/services/{$service->id}", [
            'title'           => $service->title,
            'user_id'         => $service->user_id,
            'service_type_id' => $serviceType->id,
            'introduction'    => $service->introduction,
            'media_images' => [
                 'http://google.com',
                'http://google.com',
            ],
            'media_videos'           => [
                [
                    'url' => 'http://google.com',
                    'preview' => 'http://google.com',
                ],

                [
                    'url' => 'http://yandex.com',
                    'preview' => 'http://facebook.com',
                ],
            ],
        ]);
        $response->assertOk();
        $this->assertCount(2, Service::first()->media_images);
        $this->assertCount(2, Service::first()->media_videos);

    }
    public function test_practitioner_can_see_unpublished_service()
    {
        $user = User::factory()->create(['account_type' => 'practitioner']);
        $user2 = User::factory()->create(['account_type' => 'practitioner']);
        $serviceType = ServiceType::factory()->create();
        $service    = Service::factory()->create([
            'user_id' => $user->id,
            'service_type_id' => $serviceType->id,
            'is_published' => false
        ]);
        $service2    = Service::factory()->create([
            'user_id' => $user2->id,
            'service_type_id' => $serviceType->id,
            'is_published' => false
        ]);


        $response = $this->actingAs($user)->json('get', '/api/services/practitioner');
        $response->assertOk()->assertJson([['id' => $service->id]]);
        $response->assertJsonMissing([['id' => $service2->id]]);
    }

    public function test_user_can_see_public_service_by_url_and_id()
    {
        $user = User::factory()->create(['account_type' => 'practitioner']);
        $serviceType = ServiceType::factory()->create();
        $service    = Service::factory()->create([
            'user_id' => $user->id,
            'service_type_id' => $serviceType->id,
            'keyword_id' => 1,
            'title' => 'best service',
            'url' => 'best-service'
        ]);

        $response = $this->json('get', "/api/services/{$service->id}");
        $response->assertOk();

        $response = $this->json('get', "/api/services/{$service->url}");
        $response->assertOk();
    }

    protected function creteStripeProduct()
    {
        $client = app()->make(StripeClient::class);
        return $client->products->create(['name' => 'Test product @' . now()->toDateTimeString()]);
    }
}
