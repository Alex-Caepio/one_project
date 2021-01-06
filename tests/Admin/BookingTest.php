<?php

namespace Tests\Admin;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->login($this->user);
    }

    public function test_user_can_see_booking_list(): void
    {
        $user = User::factory()->create();
        Booking::factory()->count(2)->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->json('get', "/api/bookings");
        $response
            ->assertOk();
    }

    public function test_admin_can_see_booking_list(): void
    {
        Booking::factory()->count(2)->create();
        $response = $this->actingAs($this->user)->json('get', "/admin/bookings");
        $response
            ->assertOk();
    }

}
