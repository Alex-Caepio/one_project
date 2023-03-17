<?php

namespace Tests\Api;

use App\Models\Timezone;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TimezoneTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_get_timezones()
    {
        Timezone::factory()->create();

        $response = $this->json('get','/api/timezones');
        $response->assertOk();
    }
}
