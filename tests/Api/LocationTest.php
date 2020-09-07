<?php

namespace Tests\Api;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Location;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_can_get_all_location(): void
    {
        factory(Location::class, 2)->create();
        $response = $this->json('get', "/api/locations");

        $response
            ->assertOk()->assertJsonStructure([
                ['id', 'title'],
            ]);
    }
    public function test_can_get_all_location_list(): void
    {
        $location= factory(Location::class, 2)->create();
        $response = $this->json('get', "/api/locations/list");
        $response->assertOk($location);
    }

    public function test_can_get_all_location_filter(): void
    {
        $filterOne = factory(Location::class)->create();
        $filterTwo = factory(Location::class)->create();
        $response = $this->json('get', "/api/locations/filter", [
            'title' => $filterTwo->title,
        ]);

        $response->assertOk()->assertJson([[
            'title' => $filterTwo->title,
        ]]);
    }

}
