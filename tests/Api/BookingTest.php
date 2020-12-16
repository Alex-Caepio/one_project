<?php

namespace Tests\Api;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
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
        ->assertJson([['user_id' => $booking_past->user_id, 'id' => $booking_past->id]]);

        $booking_future = Booking::factory()->create([
            'cost' => '5',
            'datetime_from' => '2020-12-31',
            'user_id'=>$this->user->id
        ]);

        $response = $this->actingAs($this->user)->json('get', "/api/bookings?status=upcoming");
        $response
            ->assertOk()
            ->assertJson([['user_id' => $booking_future->user_id, 'id' => $booking_future->id]]);

        $booking_deleted = Booking::factory()->create([
            'cost' => '5',
            'deleted_at' => '2020-11-25',
            'user_id'=>$this->user->id
        ]);

        $response = $this->actingAs($this->user)->json('get', "/api/bookings?status=canceled");
        $response
            ->assertOk()
        ->assertJson([['user_id' => $booking_deleted->user_id, 'id' => $booking_deleted->id]]);
    }

    public function test_user_can_filter_booking_by_practitioner()
    {
        $user = User::factory()->create(['account_type' => 'practitioner']);

        $service = Service::factory()->create(['user_id' => $user->id]);

        $schedule = Schedule::factory()->create(['service_id' => $service->id]);

        $booking = Booking::factory()->create([
            'cost' => '5',
            'datetime_from' => '2020-9-5',
            'user_id' => $this->user->id,
            'schedule_id' => $schedule->id,
        ]);

        $response = $this->actingAs($this->user)->json(
            'get', "/api/bookings?practitioner="
            .$user->id)
            ->assertOk()
            ->assertJson([['user_id' => $booking->user_id, 'id' => $booking->id]]);
    }

    public function test_user_can_filter_booking_by_datetime()
    {
        $booking = Booking::factory()->create([
            'cost' => '5',
            'datetime_from' => '2020-9-5',
            'datetime_to' => '2020-10-11',
            'user_id'=>$this->user->id
        ]);

        $response = $this->actingAs($this->user)->json('get', "/api/bookings?datetime_from="
            . $booking->datetime_from . 'datetime_to=' . $booking->datetime_to);
        $response
            ->assertOk()
            ->assertJson([['user_id' => $booking->user_id, 'id' => $booking->id]]);
    }

}
