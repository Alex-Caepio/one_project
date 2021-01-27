<?php

namespace Tests\Api;

use App\Models\Booking;
use App\Models\Instalment;
use App\Models\Purchase;
use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use phpDocumentor\Reflection\Location;
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
        Booking::factory()->count(5)->create(['cost' => 5]);

        $response = $this->json('get', "/api/bookings");
        $response->assertOk();
    }

    public function test_user_can_filter_booking_by_status(): void
    {
        $bookingPast = Booking::factory()->create([
            'cost' => 5,
            'datetime_from' => '2020-9-5',
            'user_id'=>$this->user->id
        ]);

        $response = $this->actingAs($this->user)->json('get', "/api/bookings?status=completed");
        $response
            ->assertOk()
        ->assertJson([['user_id' => $bookingPast->user_id, 'id' => $bookingPast->id]]);

        $bookingFuture = Booking::factory()->create([
            'cost' => 5,
            'datetime_from' => '2022-12-31',
            'user_id'=>$this->user->id
        ]);

        $response = $this->actingAs($this->user)->json('get', "/api/bookings?status=upcoming");
        $response
            ->assertOk()
            ->assertJson([['user_id' => $bookingFuture->user_id, 'id' => $bookingFuture->id]]);

        $bookingDeleted = Booking::factory()->create([
            'cost' => 5,
            'deleted_at' => '2020-11-25',
            'user_id'=>$this->user->id
        ]);

        $response = $this->actingAs($this->user)->json('get', "/api/bookings?status=canceled");
        $response
            ->assertOk()
        ->assertJson([['user_id' => $bookingDeleted->user_id, 'id' => $bookingDeleted->id]]);
    }

    public function test_user_can_filter_booking_by_practitioner(): void
    {
        $user = User::factory()->create(['account_type' => 'practitioner']);

        $service = Service::factory()->create(['user_id' => $user->id]);

        $schedule = Schedule::factory()->create(['service_id' => $service->id]);

        $booking = Booking::factory()->create([
            'cost' => 5,
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

    public function test_user_can_filter_booking_by_datetime(): void
    {
        $booking = Booking::factory()->create([
            'cost' => 5,
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

    public function test_user_can_filter_booking_by_booking_reference(): void
    {
        $booking = Booking::factory()->create([
            'cost' => 5,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->json('get', "/api/bookings?bookingReference="
            . $booking->reference);
        $response
            ->assertOk()
            ->assertJson([['user_id' => $booking->user_id, 'id' => $booking->id]]);
    }

    public function test_user_can_filter_booking_by_service_type(): void
    {
        $user = User::factory()->create(['account_type' => 'practitioner']);

        $service = Service::factory()->create(['user_id' => $user->id]);

        $schedule = Schedule::factory()->create(['service_id' => $service->id]);

        $booking = Booking::factory()->create([
            'cost' => 5,
            'datetime_from' => '2020-9-5',
            'user_id' => $this->user->id,
            'schedule_id' => $schedule->id,
        ]);

        $this->actingAs($this->user)->json(
            'get', "/api/bookings?serviceType="
            .$service->service_type_id)
            ->assertOk()
            ->assertJson([['user_id' => $booking->user_id, 'id' => $booking->id]]);
    }

    public function test_user_can_filter_booking_by_is_virtual(): void
    {
        User::factory()->create(['account_type' => 'practitioner']);


        $schedule = Schedule::factory()->create(['is_virtual' => 1]);

        $booking = Booking::factory()->create([
            'cost' => 5,
            'datetime_from' => '2020-9-5',
            'user_id' => $this->user->id,
            'schedule_id' => $schedule->id,
        ]);

        $this->actingAs($this->user)->json('get', "/api/bookings?isVirtual=virtual")
            ->assertOk()
            ->assertJson([['user_id' => $booking->user_id, 'id' => $booking->id]]);

        $scheduleReal = Schedule::factory()->create(['is_virtual' => 0]);

        $bookingReal = Booking::factory()->create([
            'cost' => 5,
            'datetime_from' => '2020-9-5',
            'user_id' => $this->user->id,
            'schedule_id' => $scheduleReal->id,
        ]);

        $this->actingAs($this->user)->json('get', "/api/bookings?isVirtual=physical")
            ->assertOk()
            ->assertJson([['user_id' => $bookingReal->user_id, 'id' => $bookingReal->id]]);
    }

    public function test_user_can_filter_booking_by_city(): void
    {
        User::factory()->create(['account_type' => 'practitioner']);


        $schedule = Schedule::factory()->create(['is_virtual' => 0]);

        $booking = Booking::factory()->create([
            'cost' => 5,
            'datetime_from' => '2020-9-5',
            'user_id' => $this->user->id,
            'schedule_id' => $schedule->id,
        ]);

        $this->actingAs($this->user)->json('get', "/api/bookings?city=".$schedule->city)
            ->assertOk()
            ->assertJson([['user_id' => $booking->user_id, 'id' => $booking->id]]);
    }

    public function test_user_can_filter_booking_by_country(): void
    {
        User::factory()->create(['account_type' => 'practitioner']);


        $schedule = Schedule::factory()->create(['is_virtual' => 0]);

        $booking = Booking::factory()->create([
            'cost' => 5,
            'datetime_from' => '2020-9-5',
            'user_id' => $this->user->id,
            'schedule_id' => $schedule->id,
        ]);

       $this->actingAs($this->user)->json('get', "/api/bookings?country=".$schedule->country)
            ->assertOk()
            ->assertJson([['user_id' => $booking->user_id, 'id' => $booking->id]]);
    }

    public function test_user_can_filter_booking_by_payment_method():void
    {
        $purchaseDeposit = Purchase::factory()->create([
            'user_id' => $this->user->id,
            'is_deposit' => true,
            ]);

        $booking = Booking::factory()->create([
            'cost' => 5,
            'user_id' => $this->user->id,
            'purchase_id' => $purchaseDeposit->id
        ]);

        $response = $this->actingAs($this->user)->json('get', "/api/bookings?paymentMethod=deposit");
        $response
            ->assertOk()
            ->assertJson([['user_id' => $booking->user_id, 'id' => $booking->id]]);

        $purchaseSingle = Purchase::factory()->create([
            'user_id' => $this->user->id,
            'is_deposit' => false,
        ]);

        $bookingSingle = Booking::factory()->create([
            'cost' => 5,
            'user_id'=>$this->user->id,
            'purchase_id' => $purchaseSingle->id,
        ]);

        $response = $this->actingAs($this->user)->json('get', "/api/bookings?paymentMethod=singlepayment");
        $response
            ->assertOk()
            ->assertJson([['user_id' => $bookingSingle->user_id, 'id' => $bookingSingle->id]]);
    }
}
