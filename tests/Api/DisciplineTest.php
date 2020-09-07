<?php

namespace Tests\Api;

use App\Models\Discipline;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DisciplineTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_can_get_all_discipline(): void
    {
        factory(Discipline::class, 2)->create();
        $response = $this->json('get', "/api/disciplines");

        $response
            ->assertOk()->assertJsonStructure([
                ['id', 'title'],
            ]);
    }

    public function test_can_get_all_discipline_list(): void
    {
        $description = factory(Discipline::class, 2)->create();
        $response = $this->json('get', "/api/disciplines/list");
        $response->assertOk($description);
    }

    public function test_can_get_all_discipline_filter(): void
    {
        $filterOne = factory(Discipline::class)->create();
        $filterTwo = factory(Discipline::class)->create();
        $response = $this->json('get', "/api/disciplines/filter", [
            'title' => $filterTwo->title,
        ]);

        $response->assertOk()->assertJson([[
            'title' => $filterTwo->title,
        ]]);
    }
    public function test_all_image_discipline(): void
    {
        $discipline = factory(Discipline::class)->create();
        $response = $this->json('get', "/api/disciplines/{$discipline->id}/images");
        $discipline->discipline_images;
        $response->assertOk();
    }

    public function test_all_video_discipline(): void
    {
        $discipline = factory(Discipline::class)->create();
        $response = $this->json('get', "/api/disciplines/{$discipline->id}/videos");
        $discipline->discipline_videos;
        $response->assertOk();
    }

}
