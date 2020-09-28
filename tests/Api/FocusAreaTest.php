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

    public function test_all_image_focus_area(): void
    {
        $focus = FocusArea::factory()->create();
        $response = $this->json('get', "/api/focus-area/{$focus->id}/images");
        $focus->focus_area_images;
        $response->assertOk();
    }

    public function test_all_video_focus_area(): void
    {
        $focus = FocusArea::factory()->create();
        $response = $this->json('get', "/api/focus-area/{$focus->id}/videos");
        $focus->focus_area_videos;
        $response->assertOk();
    }
}
