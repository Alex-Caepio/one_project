<?php

namespace Tests\Admin;

use App\Models\Service;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->login($this->user);
    }
    public function test_all_service(): void
    {
        factory(Service::class, 2)->create();
        $response = $this->json('get', "/admin/service");

        $response->assertOk();
    }
    public function test_store_service(): void
    {
        $service = factory(Service::class)->create();
        $response = $this->json('post', '/admin/service', [
            'title' => $service->title,
            'description' => $service->description,
            'keyword_id' => $service->keyword_id,
            'user_id' => $service->user_id,
            'is_published' => $service->is_published,
            'introduction' => $service->introduction,
            'url' => $service->url,
        ]);
        $response->assertOk($service);
    }
    public function test_update_service(): void
    {
        $service = factory(Service::class)->create();
        $newService = factory(Service::class)->make();

        $response = $this->json('put', "admin/service/{$service->id}",
            [
                'title' => $newService->title,
            ]);

        $response->assertOk()
            ->assertJson([
                'title' => $newService->title,
            ]);
    }
    public function test_delete_service(): void
    {
        $service = factory(Service::class)->create();
        $response = $this->json('delete', "/admin/service/{$service->id}");

        $response->assertStatus(204);
    }
}
