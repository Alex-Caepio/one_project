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
        Discipline::factory()->count(10)->create();
        $this->json('get', "/api/disciplines")
            ->assertOk();
    }

    public function test_show_discipline(): void
    {
        $discipline = Discipline::factory()->create();
        $this->json('get', "api/disciplines/{$discipline->id}")
            ->assertOk();
    }
}
