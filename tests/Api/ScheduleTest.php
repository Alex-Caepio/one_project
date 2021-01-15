<?php

namespace Tests\Api;

use App\Models\Booking;
use App\Models\Discipline;
use App\Models\FocusArea;
use App\Models\Price;
use App\Models\Promotion;
use App\Models\PromotionCode;
use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\ScheduleAvailability;
use App\Models\ScheduleFile;
use App\Models\ScheduleFreeze;
use App\Models\ScheduleHiddenFile;
use App\Models\ScheduleUnavailability;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ScheduleTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_all_schedule(): void
    {
        Schedule::factory()->count(2)->create();
        $service  = Service::factory()->create();
        $response = $this->json('get', "/api/services/{$service->id}/schedules");

        $response
            ->assertOk();
    }

    public function test_store_schedule(): void
    {
        Event::fake();
        $serviceType = ServiceType::factory()->create(['id' => 'training_program']);
        $service     = Service::factory()->create(['service_type_id' => $serviceType->id]);
        $schedule    = Schedule::factory()->make();
        $response    = $this->json('post', "api/services/{$service->id}/schedules", [
            'title'              => $schedule->title,
            'service_id'         => $service->id,
            'start_date'         => $schedule->start_date,
            'end_date'           => $schedule->end_date,
            'cost'               => $schedule->cost,
            'location_displayed' => 'Location',
            'refund_terms'       => 20,
            'madia_files'        => [
                ['url' => 'http://google.com'],
                ['url' => 'http://google.com'],
            ],
            'prices'             => [[
                'name'    => 'test',
                'cost'    => 200.00,
                'is_free' => false,
            ]],
        ]);
        $response->assertOk();
    }

    public function test_update_schedule(): void
    {
        Event::fake();
        $stripeProduct       = $this->creteStripeProduct();
        $serviceType = ServiceType::factory()->create(['id' => 'training_program']);
        $service     = Service::factory()->create(['service_type_id' => $serviceType->id, 'stripe_id' => $stripeProduct->id]);
        $schedule    = Schedule::factory()->create(['service_id' => $service->id]);
        $response    = $this->json('put', "api/schedules/{$schedule->id}", [
            'title' => '123'
        ]);
        $response->assertOk();
    }

    public function test_update_schedule_with_prices(): void
    {
        Event::fake();

        $stripeProduct       = $this->creteStripeProduct();
        $stripePriceToDelete = $this->creteStripePrice($stripeProduct->id);
        $stripePriceToUpdate = $this->creteStripePrice($stripeProduct->id);

        $serviceType   = ServiceType::factory()->create(['id' => 'training_program']);
        $service       = Service::factory()->create([
            'service_type_id' => $serviceType->id,
            'stripe_id'       => $stripeProduct->id
        ]);
        $schedule      = Schedule::factory()->create(['service_id' => $service->id]);
        $priceToDelete = Price::factory()->create(['schedule_id' => $schedule->id, 'stripe_id' => $stripePriceToDelete->id]);
        $priceToUpdate = Price::factory()->create(['schedule_id' => $schedule->id, 'stripe_id' => $stripePriceToUpdate->id]);

        $response = $this->json('put', "api/schedules/{$schedule->id}", [
            'title'  => '123',
            'prices' => [
                [
                    'name' => 'Test price to create'
                ],
                [
                    'id'   => $priceToUpdate->id,
                    'name' => 'New price name'
                ],
            ]
        ]);
        $response->assertOk();
    }

    public function test_all_user()
    {
        $schedule       = Schedule::factory()->create();
        $user           = User::factory()->create();
        $promotion_code = PromotionCode::factory()->create();
        $response       = $this->json('get', "api/schedules/{$schedule->id}/attendants", [
            'user_id'           => $user->id,
            'schedule_id'       => $schedule->id,
            'promotion_code_id' => $promotion_code->id,
        ]);
        $response->assertOk();
    }

    public function test_promo_code()
    {
        $stripeProduct  = $this->creteStripeProduct();
        $schedule    = Schedule::factory()->create();
        $old_cost    = $schedule->cost;
        $promotion   = Promotion::factory()->create([
            'status' => 'active',
            'expiry_date' => ''
        ]);
        $service     = Service::factory()->create(['id' => $schedule->service_id, 'stripe_id' => $stripeProduct->id]);
        $discipline  = Discipline::factory()->create();
        $serviceType = ServiceType::factory()->create();
        $focusArea   = FocusArea::factory()->create();
        $service->disciplines()->attach($discipline);
        $service->service_type()->associate($serviceType);
        $service->focus_areas()->attach($focusArea);
        $promoCode = PromotionCode::factory()->create(['promotion_id' => $promotion->id]);
        $response = $this->json('post', "api/schedules/{$schedule->id}/promoсode", ['promo_code' => $promoCode->name]);
        $response->assertOk();
        $this->assertFalse($old_cost == $schedule->cost);
    }

    public function test_validate_request_class_ad_hoc_schedule(): void
    {
        Event::fake();
        $stripeProduct = $this->creteStripeProduct();
        $serviceType   = ServiceType::factory()->create(['id' => 'class_ad_hoc']);
        $service       = Service::factory()->create(['service_type_id' => $serviceType->id, 'stripe_id' => $stripeProduct->id]);
        $schedule      = Schedule::factory()->make();

        $payload           = $schedule->toArray();
        $payload['prices'] = [[
            'name'    => 'test price',
            'is_free' => false,
            'cost'    => 100,
        ]];

        $response = $this->json('post', "api/services/{$service->id}/schedules", $payload);
        $response->assertOk();
    }

    public function test_validate_request_workshop_schedule(): void
    {
        Event::fake();
        $stripeProduct = $this->creteStripeProduct();
        $serviceType = ServiceType::factory()->create(['id' => 'workshop']);
        $service     = Service::factory()->create(['service_type_id' => $serviceType->id, 'stripe_id' => $stripeProduct->id]);
        $schedule    = Schedule::factory()->make();

        $payload           = $schedule->toArray();
        $payload['prices'] = [[
            'name'    => 'test price',
            'is_free' => false,
            'cost'    => 100,
            'stripe_id' => $stripeProduct->id
        ]];

        $response = $this->json('post', "api/services/{$service->id}/schedules", $payload);
        $response->assertOk();
    }

    public function test_validate_request_econtent_schedule(): void
    {
        Event::fake();
        $stripeProduct = $this->creteStripeProduct();
        $serviceType = ServiceType::factory()->create(['id' => 'econtent']);
        $service     = Service::factory()->create(['service_type_id' => $serviceType->id, 'stripe_id' => $stripeProduct->id]);
        $schedule    = Schedule::factory()->make();

        $payload           = $schedule->toArray();
        $payload['prices'] = [[
            'name'    => 'test price',
            'is_free' => false,
            'cost'    => 100,
            'stripe_id' => $stripeProduct->id
        ]];

        $response = $this->json('post', "api/services/{$service->id}/schedules", $payload);
        $response->assertOk();
    }

    public function test_validate_request_class_schedule(): void
    {
        Event::fake();
        $stripeProduct = $this->creteStripeProduct();
        $serviceType = ServiceType::factory()->create(['id' => 'class']);
        $service     = Service::factory()->create(['service_type_id' => $serviceType->id, 'stripe_id' => $stripeProduct->id]);
        $schedule    = Schedule::factory()->make();

        $payload           = $schedule->toArray();
        $payload['prices'] = [[
            'name'    => 'test price',
            'is_free' => false,
            'cost'    => 100,
            'stripe_id' => $stripeProduct->id
        ]];

        $response = $this->json('post', "api/services/{$service->id}/schedules", $payload);
        $response->assertOk();
    }

    public function test_validate_request_courses_schedule(): void
    {
        Event::fake();
        $stripeProduct = $this->creteStripeProduct();
        $serviceType = ServiceType::factory()->create(['id' => 'courses']);
        $service     = Service::factory()->create(['service_type_id' => $serviceType->id, 'stripe_id' => $stripeProduct->id]);
        $schedule    = Schedule::factory()->make();

        $payload           = $schedule->toArray();
        $payload['prices'] = [[
            'name'    => 'test price',
            'is_free' => false,
            'cost'    => 100,
            'stripe_id' => $stripeProduct->id
        ]];

        $response = $this->json('post', "api/services/{$service->id}/schedules", $payload);
        $response->assertOk();
    }

    public function test_validate_request_events_schedule(): void
    {
        Event::fake();
        $stripeProduct = $this->creteStripeProduct();
        $serviceType = ServiceType::factory()->create(['id' => 'events']);
        $service     = Service::factory()->create(['service_type_id' => $serviceType->id, 'stripe_id' => $stripeProduct->id]);
        $schedule    = Schedule::factory()->make();

        $payload           = $schedule->toArray();
        $payload['prices'] = [[
            'name'    => 'test price',
            'is_free' => false,
            'cost'    => 100,
            'stripe_id' => $stripeProduct->id
        ]];

        $response = $this->json('post', "api/services/{$service->id}/schedules", $payload);
        $response->assertOk();
    }

    public function test_validate_request_product_schedule(): void
    {
        Event::fake();
        $stripeProduct = $this->creteStripeProduct();
        $serviceType = ServiceType::factory()->create(['id' => 'product']);
        $service     = Service::factory()->create(['service_type_id' => $serviceType->id, 'stripe_id' => $stripeProduct->id]);
        $schedule    = Schedule::factory()->make();

        $payload           = $schedule->toArray();
        $payload['prices'] = [[
            'name'    => 'test price',
            'is_free' => false,
            'cost'    => 100,
            'stripe_id' => $stripeProduct->id
        ]];

        $response = $this->json('post', "api/services/{$service->id}/schedules", $payload);
        $response->assertOk();
    }

    public function test_validate_request_retreat_schedule(): void
    {
        Event::fake();
        $stripeProduct = $this->creteStripeProduct();
        $serviceType = ServiceType::factory()->create(['id' => 'retreat']);
        $service     = Service::factory()->create(['service_type_id' => $serviceType->id, 'stripe_id' => $stripeProduct->id]);
        $schedule    = Schedule::factory()->make();

        $payload           = $schedule->toArray();
        $payload['prices'] = [[
            'name'    => 'test price',
            'is_free' => false,
            'cost'    => 100,
            'stripe_id' => $stripeProduct->id
        ]];

        $response = $this->json('post', "api/services/{$service->id}/schedules", $payload);
        $response->assertOk();
    }

    public function test_validate_request_training_program_schedule(): void
    {
        Event::fake();
        $stripeProduct = $this->creteStripeProduct();
        $serviceType = ServiceType::factory()->create(['id' => 'training_program']);
        $service     = Service::factory()->create(['service_type_id' => $serviceType->id, 'stripe_id' => $stripeProduct->id]);
        $schedule    = Schedule::factory()->make();

        $payload           = $schedule->toArray();
        $payload['prices'] = [[
            'name'    => 'test price',
            'is_free' => false,
            'cost'    => 100,
            'stripe_id' => $stripeProduct->id
        ]];

        $response = $this->json('post', "api/services/{$service->id}/schedules", $payload);
        $response->assertOk();
    }

    public function test_saving_apointment_schedule_with_relations(): void
    {
        Event::fake();
        $stripeProduct = $this->creteStripeProduct();
        $serviceType = ServiceType::factory()->create(['id' => 'appointment']);
        $service     = Service::factory()->create(['service_type_id' => $serviceType->id, 'stripe_id' => $stripeProduct->id]);

        $prices                   = Price::factory()->count(2)->make(['stripe_id' => $stripeProduct->id]);
        $schedule                 = Schedule::factory()->make();
        $scheduleFiles            = ScheduleFile::factory()->count(3)->make();
        $scheduleHiddenFiles      = ScheduleHiddenFile::factory()->count(3)->make();
        $scheduleAvailabilities   = ScheduleAvailability::factory()->count(2)->make();
        $scheduleUnavailabilities = ScheduleUnavailability::factory()->count(2)->make();

        $payload                              = $schedule->toArray();
        $payload['prices']                    = $prices->toArray();
        $payload['schedule_files']            = $scheduleFiles->toArray();
        $payload['schedule_hidden_files']     = $scheduleHiddenFiles->toArray();
        $payload['schedule_availabilities']   = $scheduleAvailabilities->toArray();
        $payload['schedule_unavailabilities'] = $scheduleUnavailabilities->toArray();

        $response = $this->json('post', "api/services/{$service->id}/schedules", $payload);
        $response->assertOk();

        $schedule = Schedule::find($response->getOriginalContent()['id']);
        self::assertCount(2, $schedule->schedule_unavailabilities);
        self::assertCount(2, $schedule->schedule_availabilities);
        self::assertCount(2, $schedule->prices);
        self::assertCount(3, $schedule->schedule_files);
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
        $unavailability = ScheduleUnavailability::factory()->create([
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


    public function test_schedule_update_creates_reschedules()
    {
        Event::fake();
        $stripeProduct = $this->creteStripeProduct();
        $service  = Service::factory()->create(['stripe_id' => $stripeProduct->id]);
        $schedule = Schedule::factory()->create(['service_id' => $service->id, 'attendees' => 1]);
        $bookings = Booking::factory()->create(['schedule_id' => $schedule->id]);

        $rescheduleRequest = RescheduleRequest::factory()->create([
            'booking_id'  => $bookings->id,
            'schedule_id' => $schedule->id
        ]);

        $this->json('put', "api/schedules/{$schedule->id}", [
            'location_id'        => 12345,
            'location_displayed' => '123asd',
        ])->assertOk();

        $this->assertDeleted('reschedule_requests', ['id' => $rescheduleRequest->id]);
        $this->assertDatabaseHas('reschedule_requests', [
            'new_location_displayed' => '123asd',
        ]);

    }

    public function test_reschedule_is_created_on_availability_change()
    {
        Event::fake();
        $stripeProduct = $this->creteStripeProduct();
        $service  = Service::factory()->create(['stripe_id' => $stripeProduct->id]);
        $schedule = Schedule::factory()->create([
            'service_id' => $service->id,
            'attendees'  => 1
        ]);
        ScheduleAvailability::factory()->create([
            'schedule_id' => $schedule->id,
            'days'        => 'monday',
            'start_time'  => '10:00:00',
            'end_time'    => '18:00:00',
        ]);
        Booking::factory()->create([
            'schedule_id'   => $schedule->id,
            'datetime_from' => '2020-12-21 17:00:00'
        ]);

        $this->json('put', "api/schedules/$schedule->id", [
            ['availabilities' =>
                 [
                     'days'       => 'monday',
                     'start_time' => '09:00:00',
                     'end_time'   => '15:00:00'
                 ]
            ]
        ])->assertStatus(200);

        $this->assertDatabaseCount('reschedule_requests', 1);
    }

    public function test_reschedule_is_created_on_unavailability_change()
    {
        Event::fake();
        $stripeProduct = $this->creteStripeProduct();
        $service  = Service::factory()->create(['stripe_id' => $stripeProduct->id]);
        $schedule = Schedule::factory()->create([
            'service_id' => $service->id,
            'attendees'  => 1
        ]);
        ScheduleAvailability::factory()->create([
            'schedule_id' => $schedule->id,
            'days'        => 'monday',
            'start_time'  => '10:00:00',
            'end_time'    => '18:00:00',
        ]);
        Booking::factory()->create([
            'schedule_id'   => $schedule->id,
            'datetime_from' => '2020-12-21 17:00:00'
        ]);

        $this->json('put', "api/schedules/$schedule->id", [
            ['unavaulabilities' =>
                 [
                     'start_date' => '2020-12-21 10:00:00',
                     'end_date'   => '2020-12-23 18:00:00'
                 ]
            ]
        ])->assertStatus(200);

        $this->assertDatabaseCount('reschedule_requests', 1);
    }

    protected function creteStripeProduct()
    {
        $client = app()->make(StripeClient::class);
        return $client->products->create(['name' => 'Test product @' . now()->toDateTimeString()]);
    }

    protected function creteStripePrice($product)
    {
        $client = app()->make(StripeClient::class);
        return $client->prices->create([
            'unit_amount' => '1000',
            'currency'    => 'usd',
            'product'     => $product,
        ]);
    }
}


