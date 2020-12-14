<?php

namespace Tests\Api;

use App\Models\Booking;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_user_can_only_see_their_own_booking_list(): void
    {
        Booking::factory()->count(5)->create();

        $response = $this->json('get', "/api/bookings");
        $response->assertOk();
    }

}
