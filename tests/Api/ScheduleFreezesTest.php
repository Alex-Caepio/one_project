<?php


namespace Tests\Api;

use App\Models\ScheduleFreeze;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ScheduleFreezesTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_can_get_all_ScheduleFreezes(): void
    {
        ScheduleFreeze::factory()->create();
        $response = $this->json('get', "/api/schedule-freezes");

        $response->assertOk();
    }
}
