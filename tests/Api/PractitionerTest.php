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
        factory(User::class, 2)->create()->where('account_type', 'practitioner');
        $response = $this->json('get', "/api/practitioners");
        $response->assertOk();
    }

    public function test_can_get_all_user_practitioner_list(): void
    {
        $userPractitioner = factory(User::class, 2)->create()->where('account_type', 'practitioner');;
        $response = $this->json('get', "/api/service_types/list");
        $response->assertOk($userPractitioner);
    }

    public function test_store_favorite(): void
    {
        $authUser = factory(User::class)->create();
        $userId = factory(User::class)->create();
        $response = $this->json('post', "practitioners/{$userId->id}/favourite");
        $authUser->favourite_practitioners()->attach($userId);

        $this->assertDatabaseHas('practitioner_favorites', [
            'user_id' => $authUser->id,
            'practitioner_id' => $userId->id
        ]);
    }

}
