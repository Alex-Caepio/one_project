<?php

namespace Tests\Api;

use App\Models\FocusArea;
use Tests\TestCase;

class FocusAreaTest extends TestCase
{
    public function test_all_image_focus_area(): void
    {
        $focus = factory(FocusArea::class)->create();
        $response = $this->json('get', "/api/focus-area/{$focus->id}/images");
        $focus->focus_area_images;
        $response->assertOk();
    }

    public function test_all_video_focus_area(): void
    {
        $focus = factory(FocusArea::class)->create();
        $response = $this->json('get', "/api/focus-area/{$focus->id}/videos");
        $focus->focus_area_videos;
        $response->assertOk();
    }
}
