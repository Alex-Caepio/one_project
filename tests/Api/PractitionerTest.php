<?php

namespace Tests\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PractitionerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_can_see_user_practitioner(): void
    {
        User::factory()->count(2)->create();
        $response = $this->json('get', "/api/practitioners");
        $response->assertOk();
    }

    public function test_can_get_all_user_practitioner_list(): void
    {
        User::factory()->count(2)->create();
        $response = $this->json('get', "/api/service_types/list");
        $response->assertOk();
    }

    public function test_store_favorite(): void
    {
        $authUser = User::factory()->create();
        $userId = User::factory()->create();
        $response = $this->json('post', "api/practitioners/{$userId->id}/favourite");
        $authUser->favourite_practitioners()->attach($userId);

        $this->assertDatabaseHas('practitioner_favorites', [
            'user_id' => $authUser->id,
            'practitioner_id' => $userId->id
        ]);
    }
    public function test_delete_practitioner_favorite(): void
    {
        $practitioner = User::factory()->create();
        $response = $this->json('delete', "api/practitioners/{$practitioner->id}/favourite");

        $response->assertStatus(204);
    }

}
