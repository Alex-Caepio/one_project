<?php

namespace Tests\Api;

use App\Models\Booking;
use App\Models\Country;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CancellationTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }
    public function test_user_can_cancell_many_bookigns(): void
    {
        $bookings = Booking::factory()->count(2)->create(['practitioner_id' => $this->user->id]);
        $response = $this->json('post', '/api/cancellations/bookings', [
            'booking_ids' => $bookings->pluck('id')
        ]);

        $response->assertOk();
    }
}
