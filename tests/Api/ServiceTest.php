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
        $stripeProduct = $this->creteStripeProduct();
        $service       = Service::factory()->create([
            'user_id'         => $user->id,
            'service_type_id' => $service_type->id,
            'stripe_id'       => $stripeProduct->id
        ]);;
        $newService = Service::factory()->make();

        $response = $this->actingAs($user)->json('put', "/api/services/{$service->id}",
            [
                'title' => $newService->title,
                'service_type_id' => $service_type->id,
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
        $keyword = Keyword::factory()->create();
        $service    = Service::factory()->create([
            'user_id'         => $user->id,
            'service_type_id' => $service_type->id,
            'keyword_id'        => $keyword->id,
        ]);

        $response = $this->actingAs($user)->json('put', "/api/services/{$service->id}", [
            'title'           => $service->title,
            'user_id'         => $service->user_id,
            'service_type_id' => $service_type->id,
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
        $service_type = ServiceType::factory()->create();
        $service    = Service::factory()->create([
            'user_id' => $user->id,
            'service_type_id' => $service_type->id,
            'keyword_id' => 1
        ]);

        $response = $this->actingAs($user)->json('put', "/api/services/{$service->id}", [
            'title'        => $service->title,
            'user_id'      => $service->user_id,
            'service_type_id' => $service_type->id,
            'introduction' => $service->introduction,
            'keywords'     => [],
        ]);

        $response->assertOk();
        $this->assertCount(0, Service::first()->keywords);
    }

    protected function creteStripeProduct()
    {
        $client = app()->make(StripeClient::class);
        return $client->products->create(['name' => 'Test product @' . now()->toDateTimeString()]);
    }
}
