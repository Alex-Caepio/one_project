<?php

namespace Tests\Api;

use App\Models\FocusArea;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FocusAreaTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_can_get_all_focus_area(): void
    {
        FocusArea::factory()->make();
        $response = $this->json('get', "/api/focus-areas");

        $response->assertOk();
    }

    public function test_show_focus_area(): void
    {
        $focusArea = FocusArea::factory()->make();
        $response = $this->json('get', "api/focus-areas/{$focusArea->id}");

        $response->assertOk();
    }

    public function test_all_image_focus_area(): void
    {
        $focusArea = FocusArea::factory()->create();
        $response = $this->json('get', "api/focus-areas/{$focusArea->id}/images");
        $focusArea->focus_area_images;
        $response->assertOk();
    }

    public function test_all_video_focus_area(): void
    {
        $focus = FocusArea::factory()->create();
        $response = $this->json('get', "/api/focus-areas/{$focus->id}/videos");
        $focus->focus_area_videos;
        $response->assertOk();
    }
}
