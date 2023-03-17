<?php

namespace Tests\Api;

use App\Models\Country;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CountryTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }
    public function test_get_all_country_filter(): void
    {
        $countryNew = Country::factory()->create();
        $response = $this->json('get', "/api/countries", [
            'iso' => $countryNew->iso,
            'name' => $countryNew->name,
            'nicename' => $countryNew->nicename,
            'iso3' => $countryNew->iso3,
        ]);

        $response->assertOk($countryNew);
    }
}
