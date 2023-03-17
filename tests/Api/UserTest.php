<?php

namespace Tests\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_can_get_all_service_favorites(): void
    {
        $user = User::factory()->create();
        $response = $this->json('get', "/api/services/favourites");
        $user->favourite_services;
        $response->assertOk();
    }
    public function test_can_get_all_article_favorites(): void
    {
        $user = User::factory()->create();
        $response = $this->json('get', "/api/articles/favourites");
        $user->favourite_articles;
        $response->assertOk();
    }
    public function test_can_get_all_practitioner_favorites(): void
    {
        $user = User::factory()->create();
        $response = $this->json('get', "/api/practitioners/favourites");
        $user->favourite_practitioners;
        $response->assertOk();
    }
}
