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
        Booking::factory()->count(5)->create(['cost' => '5']);

        $response = $this->json('get', "/api/bookings");
        $response->assertOk();
    }

    public function test_user_can_filter_booking_by_status(): void
    {
        $booking_past = Booking::factory()->create([
            'cost' => '5',
            'datetime_from' => '2020-9-5',
            'user_id'=>$this->user->id
        ]);

        $response = $this->actingAs($this->user)->json('get', "/api/bookings?status=completed");
        $response
            ->assertOk()
        ->assertJson([['user_id' => $booking_past->user_id]]);

        $booking_future = Booking::factory()->create([
            'cost' => '5',
            'datetime_from' => '2020-12-31',
            'user_id'=>$this->user->id
        ]);

        $response = $this->actingAs($this->user)->json('get', "/api/bookings?status=upcoming");
        $response
            ->assertOk()
            ->assertJson([['user_id' => $booking_future->user_id]]);

        $booking_deleted = Booking::factory()->create([
            'cost' => '5',
            'deleted_at' => '2020-11-25',
            'user_id'=>$this->user->id
        ]);

        $response = $this->actingAs($this->user)->json('get', "/api/bookings?status=canceled");
        $response
            ->assertOk()
        ->assertJson([['user_id' => $booking_deleted->user_id]]);
    }
}
