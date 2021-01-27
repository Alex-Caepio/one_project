<?php

namespace Tests\Api;

use App\Models\Booking;
use App\Models\Price;
use App\Models\Schedule;
use App\Models\ScheduleAvailability;
use App\Models\ScheduleFreeze;
use App\Models\ScheduleUnavailability;
use App\Models\Service;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SchedulePurchaseTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_appointment_purchase_success()
    {
        $stripeProduct = $this->creteStripeProduct();
        $service      = Service::factory()->create(['service_type_id' => 'appointment', 'stripe_id' => $stripeProduct->id]);
        $schedule     = Schedule::factory()->create(['service_id' => $service->id]);
        $price        = Price::factory()->create(['schedule_id' => $schedule->id, 'stripe_id' => $stripeProduct->id]);
        $availability = ScheduleAvailability::factory()->create(
            [
                'schedule_id' => $schedule->id,
                'days'        => 'everyday',
                'start_time'  => '10:00',
                'end_time'    => '18:00'
            ]
        );

        $response = $this->json('post', "api/schedules/{$schedule->id}/purchase", [
            'price_id'       => $price->id,
            'availabilities' => [
                [
                    'availability_id' => $availability->id,
                    'datetime_from'   => '2020-11-30 11:00:00',
                ],
                [
                    'availability_id' => $availability->id,
                    'datetime_from'   => '2020-11-30 13:00:00',
                ]
            ]
        ]);

        $response->assertOk();
        $this->assertDatabaseCount('bookings', 2);
    }

    public function test_appointment_purchase_failure_due_unavailability()
    {
        $stripeProduct = $this->creteStripeProduct();
        $service        = Service::factory()->create(['service_type_id' => 'appointment', 'stripe_id' => $stripeProduct->id]);
        $schedule       = Schedule::factory()->create(['service_id' => $service->id]);
        $price          = Price::factory()->create(['schedule_id' => $schedule->id, 'stripe_id' => $stripeProduct->id]);
        $availability   = ScheduleAvailability::factory()->create(
            [
                'schedule_id' => $schedule->id,
                'days'        => 'everyday',
                'start_time'  => '10:00',
                'end_time'    => '18:00'
            ]
        );
        ScheduleUnavailability::factory()->create([
            'schedule_id' => $schedule->id,
            'start_date'  => '2020-11-30 15:00',
            'end_date'    => '2020-11-30 17:00'
        ]);

        $response = $this->json('post', "api/schedules/{$schedule->id}/purchase", [
            'price_id'       => $price->id,
            'availabilities' => [
                [
                    'availability_id' => $availability->id,
                    'datetime_from'   => '2020-11-30 16:00:00',
                ]
            ]
        ]);
        $response->status(422);
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_appointment_purchase_failure()
    {
        $stripeProduct = $this->creteStripeProduct();
        $service      = Service::factory()->create(['service_type_id' => 'appointment', 'stripe_id' => $stripeProduct->id]);
        $schedule     = Schedule::factory()->create(['service_id' => $service->id]);
        $price        = Price::factory()->create(['schedule_id' => $schedule->id, 'stripe_id' => $stripeProduct->id]);
        $availability = ScheduleAvailability::factory()->create(
            [
                'schedule_id' => $schedule->id,
                'days'        => 'everyday',
                'start_time'  => '10:00',
                'end_time'    => '18:00'
            ]
        );

        $response = $this->json('post', "api/schedules/{$schedule->id}/purchase", [
            'price_id'       => $price->id,
            'availabilities' => [
                [
                    'availability_id' => $availability->id,
                    'datetime_from'   => '2020-11-30 11:00:00',
                ],
                [
                    'availability_id' => $availability->id,
                    'datetime_from'   => '2020-11-30 19:00:00',
                ]
            ]
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_schedule_purchase_correct_price()
    {
        $stripeProduct = $this->creteStripeProduct();
        $service      = Service::factory()->create(['stripe_id' => $stripeProduct->id]);
        $schedule     = Schedule::factory()->create(['service_id' => $service->id]);
        $price        = Price::factory()->create([
            'schedule_id' => $schedule->id,
            'cost' => 1234,
            'stripe_id' => $stripeProduct->id
        ]);
        $availability = ScheduleAvailability::factory()->create(
            [
                'schedule_id' => $schedule->id,
                'days'        => 'everyday',
                'start_time'  => '10:00',
                'end_time'    => '18:00'
            ]
        );

        $response = $this->json('post', "api/schedules/{$schedule->id}/purchase",
            [
                'price_id'       => $price->id,
                'schedule_id'    => $schedule->id,
                'availabilities' => [[
                    'availability_id' => $availability->id,
                    'datetime_from'   => '2020-11-30 11:00:00'
                ]]
            ]);

        $response->assertOk();
        $this->assertEquals(1234, Booking::first()->cost);
    }

    public function test_user_cant_purchase_schedule_with_incorrect_price_id()
    {
        $stripeProduct = $this->creteStripeProduct();
        $service  = Service::factory()->create(['stripe_id' => $stripeProduct->id]);
        $schedule = Schedule::factory()->create(['service_id' => $service->id]);
        Price::factory()->create([
            'schedule_id' => $schedule->id,
            'cost' => 1234,
            'stripe_id' => $stripeProduct->id
        ]);
        $wrongPrice   = Price::factory()->create([
            'schedule_id' => 999999,
            'cost' => 1234,
            'stripe_id' => $stripeProduct->id
        ]);
        $availability = ScheduleAvailability::factory()->create(
            [
                'schedule_id' => $schedule->id,
                'days'        => 'everyday',
                'start_time'  => '10:00',
                'end_time'    => '18:00'
            ]
        );

        $response = $this->json('post', "api/schedules/{$schedule->id}/purchase",
            [
                'price_id'       => $wrongPrice->id,
                'schedule_id'    => $schedule->id,
                'availabilities' => [[
                    'availability_id' => $availability->id,
                    'datetime_from'   => '2020-11-30 11:00:00'
                ]]
            ]);

        $response->assertStatus(422)->assertJsonFragment(['errors' => ['price_id' => ['Price does not belong to the schedule']]]);
        $this->assertDatabaseCount('bookings', 0);
    }

    /**
     * $booking->datetime_to is being calculated durring the schedule purchase.
     * This test ensures that datetime_to has been calculated correctly
     */
    public function test_schedule_purchase_calculates_correct_datetime_to()
    {
        $stripeProduct = $this->creteStripeProduct();
        $service      = Service::factory()->create(['service_type_id' => 'appointment', 'stripe_id' => $stripeProduct->id]);
        $schedule     = Schedule::factory()->create(['service_id' => $service->id]);
        $availability = ScheduleAvailability::factory()->create([
            'schedule_id' => $schedule->id,
            'days'        => 'everyday',
            'start_time'  => '10:00',
            'end_time'    => '18:00'
        ]);
        $price        = Price::factory()->create([
            'schedule_id' => $schedule->id,
            'duration'    => 5,
            'stripe_id' => $stripeProduct->id
        ]);

        $response = $this->json('post', "api/schedules/{$schedule->id}/purchase", [
            'price_id'       => $price->id,
            'schedule_id'    => $schedule->id,
            'availabilities' => [[
                'availability_id' => $availability->id,
                'datetime_from'   => '2020-11-30 11:00:00'
            ]]
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('bookings', ['datetime_to' => '2020-11-30 11:05:00']);

    }

    public function test_schedule_is_sold_out()
    {
        $stripeProduct = $this->creteStripeProduct();
        $service      = Service::factory()->create(['stripe_id' => $stripeProduct->id]);
        $schedule     = Schedule::factory()->create([
            'service_id' => $service->id,
            'attendees'  => 1
        ]);
        $availability = ScheduleAvailability::factory()->create([
            'schedule_id' => $schedule->id,
            'days'        => 'everyday',
            'start_time'  => '10:00',
            'end_time'    => '18:00'
        ]);
        $price        = Price::factory()->create(['schedule_id' => $schedule->id, 'stripe_id' => $stripeProduct->id]);
        $response     = $this->json('post', "api/schedules/{$schedule->id}/purchase", [
            'price_id'       => $price->id,
            'schedule_id'    => $schedule->id,
            'availabilities' => [[
                'availability_id' => $availability->id,
                'datetime_from'   => '2020-11-30 11:00:00'
            ]]
        ]);
        $response->assertOk();

        ScheduleFreeze::factory()->create([
            'schedule_id' => $schedule->id,
            'freeze_at'   => Carbon::now()
        ]);

        $response = $this->json('post', "api/schedules/{$schedule->id}/purchase", [
            'price_id'       => $price->id,
            'schedule_id'    => $schedule->id,
            'availabilities' => [[
                'availability_id' => $availability->id,
                'datetime_from'   => '2020-11-30 11:00:00'
            ]]
        ]);
        $response->assertStatus(422)
            ->assertJsonFragment(['schedule_id' => ['All quotes on the schedule are sold out']]);
    }
}


