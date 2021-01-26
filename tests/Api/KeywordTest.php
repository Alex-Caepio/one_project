<?php

namespace Tests\Api;

use App\Models\Keyword;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KeywordTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_can_get_all_keyword(): void
    {
        Keyword::factory()->count(2)->create();
        $response = $this->json('get', "/api/keywords");

        $response
            ->assertOk()->assertJsonStructure([
                ['id', 'title'],
            ]);
    }

    public function test_can_get_all_keyword_list(): void
    {
        $keyword = Keyword::factory()->count(2)->create();
        $response = $this->json('get', "/api/keywords/list");
        $response->assertOk($keyword);
    }

    public function test_can_get_all_keyword_filter(): void
    {
        Keyword::factory()->create();
        $filterTwo = Keyword::factory()->create();
        $response = $this->json('get', "/api/keywords/filter", [
            'title' => $filterTwo->title,
        ]);

        $response->assertOk()->assertJson([[
            'title' => $filterTwo->title,
        ]]);
    }
}
