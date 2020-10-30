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
        FocusArea::factory()->create();
        $response = $this->json('get', "/api/focus-areas");

        $response->assertOk();
    }

    public function test_show_focus_area(): void
    {
        $focusArea = FocusArea::factory()->create();
        $response = $this->json('get', "api/focus-areas/{$focusArea->id}");

        $response->assertOk();
    }
}
