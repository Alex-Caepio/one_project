<?php

namespace Tests\Api;

use App\Models\Booking;
use App\Models\Discipline;
use App\Models\FocusArea;
use App\Models\Price;
use App\Models\Promotion;
use App\Models\PromotionCode;
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
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

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
        $service = Service::factory()->create();
        $response = $this->json('get', "/api/services/{$service->id}/schedules");

        $response
            ->assertOk();
    }

    public function test_store_schedule(): void
    {
        $service = Service::factory()->create(['service_type_id' => 1]);
        $schedule = Schedule::factory()->make();
        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'start_date' => $schedule->start_date,
            'end_date' => $schedule->end_date,
            'cost' => $schedule->cost,
            'madia_files' => [
                ['url' => 'http://google.com'],
                ['url' => 'http://google.com'],
            ],
        ]);
        $response->assertOk();
    }

    public function test_all_user()
    {
        $schedule = Schedule::factory()->create();
        $user = User::factory()->create();
        $promotion_code = PromotionCode::factory()->create();
        $response = $this->json('get', "api/schedule/{$schedule->id}/attendants", [
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'promotion_code_id' => $promotion_code->id,
        ]);
        $reschedule = $schedule->users();
        $response->assertOk($reschedule);
    }

    public function test_purchase()
    {
        $schedule = Schedule::factory()->create();
        $user = User::factory()->create();
        $this->json('post', "api/schedules/{$schedule->id}/purchase");
        $schedule->users()->attach($user->id);
    }

    public function test_promo_code()
    {
        $schedule = Schedule::factory()->create();
        $promotion = Promotion::factory()->create();
        $service = Service::factory()->create(['id' => $schedule->service_id]);
        $discipline = Discipline::factory()->create();
        $serviceType = ServiceType::factory()->create();
        $focusArea = FocusArea::factory()->create();
        $service->disciplines()->attach($discipline);
        $service->service_types()->attach($serviceType);
        $service->focus_areas()->attach($focusArea);
        $promoCode = PromotionCode::factory()->create(['promotion_id' => $promotion->id]);

        $response = $this->json('post', "api/schedules/{$schedule->id}/promoсodes", ['promo_code' => $promoCode->name]);
        $response->assertOk();
    }

    public function test_validate_request_class_ad_hoc_schedule(): void
    {
        $schedule = Schedule::factory()->make();
        $service = Service::factory()->create(['service_type_id' => 'class_ad_hoc']);
        $price = Price::factory()->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'prices' => $price->pluck('id')
        ]);
        $response->assertJsonFragment($schedule->prices->toArray());
        $response->assertOk();

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(1, $schedule->prices);
    }

    public function test_validate_request_workshop_schedule(): void
    {
        $schedule = Schedule::factory()->make();
        $service = Service::factory()->create(['service_type_id' => 'workshop']);
        $price = Price::factory()->create();
        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'prices' => $price->pluck('id')
        ]);
        $response->assertOk();
        $response->assertJsonFragment($schedule->prices->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(1, $schedule->prices);
    }

    public function test_validate_request_econtent_schedule(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'econtent']);
        $schedule = Schedule::factory()->make();
        $price = Price::factory()->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'prices' => $price->pluck('id')
        ]);
        $response->assertOk();
        $response->assertJsonFragment($schedule->prices->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(1, $schedule->prices);
    }

    public function test_validate_request_class_schedule(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'class']);
        $schedule = Schedule::factory()->make();
        $price = Price::factory()->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'prices' => $price->pluck('id')
        ]);
        $response->assertOk();
        $response->assertJsonFragment($schedule->prices->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(1, $schedule->prices);
    }

    public function test_validate_request_cources_schedule(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'cources']);
        $schedule = Schedule::factory()->make();
        $price = Price::factory()->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'prices' => $price->pluck('id')
        ]);
        $response->assertOk();
        $response->assertJsonFragment($schedule->prices->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(1, $schedule->prices);
    }

    public function test_validate_request_events_schedule(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'events']);
        $schedule = Schedule::factory()->make();
        $price = Price::factory()->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'prices' => $price->pluck('id')
        ]);
        $response->assertOk();
        $response->assertJsonFragment($schedule->prices->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(1, $schedule->prices);
    }

    public function test_validate_request_product_schedule(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'product']);
        $schedule = Schedule::factory()->make();
        $price = Price::factory()->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'prices' => $price->pluck('id')
        ]);
        $response->assertOk();
        $response->assertJsonFragment($schedule->prices->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(1, $schedule->prices);
    }

    public function test_validate_request_retreat_schedule(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'retreat']);
        $schedule = Schedule::factory()->make();
        $price = Price::factory()->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'prices' => $price->pluck('id')
        ]);
        $response->assertOk();
        $response->assertJsonFragment($schedule->prices->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(1, $schedule->prices);
    }

    public function test_validate_request_raining_program_schedule(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'training_program']);
        $schedule = Schedule::factory()->make();
        $price = Price::factory()->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'prices' => $price->pluck('id')
        ]);
        $response->assertOk();
        $response->assertJsonFragment($schedule->prices->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(1, $schedule->prices);
    }

    public function test_saving_apointment_schedule_with_relations(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'appointment']);
        $schedule = Schedule::factory()->make();
        $scheduleUnavailabilities = ScheduleUnavailability::factory()->count(2)->create();
        $scheduleAvailabilities = ScheduleAvailability::factory()->count(2)->create();
        $price = Price::factory()->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'schedule_unavailabilities' => $scheduleUnavailabilities->pluck('id'),
            'schedule_availabilities' => $scheduleAvailabilities->pluck('id'),
            'prices' => $price->pluck('id')
        ]);
        $response->assertOk();
        $response->assertJsonFragment($schedule->prices->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(2, $schedule->schedule_unavailabilities);
        self::assertCount(2, $schedule->schedule_availabilities);
        self::assertCount(1, $schedule->prices);
    }

    public function test_saving_schedule_files_relationships_schedules(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'appointment']);
        $schedule = Schedule::factory()->make();
        $scheduleFiles = ScheduleFile::factory()->count(3)->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'schedule_files' => $scheduleFiles->pluck('id'),
        ]);

        $response->assertOk();
        $response->assertJsonFragment($schedule->schedule_files->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(3, $schedule->schedule_files);
    }

    public function test_saving_schedule_hidden_files_relationships_schedules(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'appointment']);
        $schedule = Schedule::factory()->make();
        $scheduleHiddenFiles = ScheduleHiddenFile::factory()->count(3)->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'schedule_hidden_files' => $scheduleHiddenFiles->pluck('id'),
        ]);

        $response->assertOk();
        $response->assertJsonFragment($schedule->schedule_hidden_files->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(3, $schedule->schedule_hidden_files);
    }



    public function test_appointment_purchase_success()
    {
        $service      = Service::factory()->create(['service_type_id' => 'appointment']);
        $schedule     = Schedule::factory()->create(['service_id' => $service->id]);
        $price        = Price::factory()->create(['schedule_id' => $schedule->id]);
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
        $service      = Service::factory()->create(['service_type_id' => 'appointment']);
        $schedule     = Schedule::factory()->create(['service_id' => $service->id]);
        $price        = Price::factory()->create(['schedule_id' => $schedule->id]);
        $availability = ScheduleAvailability::factory()->create(
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
        $service      = Service::factory()->create(['service_type_id' => 'appointment']);
        $schedule     = Schedule::factory()->create(['service_id' => $service->id]);
        $price        = Price::factory()->create(['schedule_id' => $schedule->id]);
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
           $service      = Service::factory()->create();
           $schedule     = Schedule::factory()->create(['service_id' => $service->id]);
           $price        = Price::factory()->create(['schedule_id' => $schedule->id]);
           $booking = Booking::factory()->create([
               'schedule_id' => $schedule->id,
               'price_id'    => $price->id
           ]);

           $response = $this->json('post', "api/schedules/{$schedule->id}/purchase", $booking->toArray());

           $response->assertStatus(422);
           $this->assertDatabaseCount('bookings', 1);
    }

    public function test_schedule_purchase_wrong_price()
    {
        $service      = Service::factory()->create();
        $schedule     = Schedule::factory()->create(['service_id' => $service->id]);
        $price        = Price::factory()->create();
        $booking = Booking::factory()->create([
            'schedule_id' => $schedule->id,
            'price_id'    => $price->id
        ]);

        $response = $this->json('post', "api/schedules/{$schedule->id}/purchase", $booking->toArray());

        $response->assertStatus(422);
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_schedule_purchase_correct_datetime_from()
    {
        $service      = Service::factory()->create(['service_type_id' => 'appointment']);
        $schedule     = Schedule::factory()->create(['service_id' => $service->id]);
        $availability = ScheduleAvailability::factory()->create(['schedule_id' => $schedule->id]);
        $price        = Price::factory()->create([
            'schedule_id' => $schedule->id,
            'duration'    => 5
        ]);

        $response = $this->json('post', "api/schedules/{$schedule->id}/purchase", [
            'price_id'       => $price->id,
            'schedule_id'    => $schedule->id,
            'availabilities' => [
                'availability_id' => $availability->id,
                'datetime_from'   => '2020-11-30 11:00:00'
            ]
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('bookings', ['datetime_to' => '2020-11-30 11:05:00']);

    }

    public function test_schedule_is_sold_out()
    {
        $service      = Service::factory()->create();
        $schedule     = Schedule::factory()->create([
            'service_id' => $service->id,
            'attendees'  => 1
        ]);
        $price        = Price::factory()->create();
        $booking = Booking::factory()->create([
            'schedule_id' => $schedule->id,
            'price_id'    => $price->id
        ]);

        $response = $this->json('post', "api/schedules/{$schedule->id}/purchase", $booking->toArray());
        $response->assertOk();

        ScheduleFreeze::factory()->create([
            'schedule_id' => $schedule->id,
            'freeze_at'   => Carbon::now()
        ]);

        $response = $this->json('post', "api/schedules/{$schedule->id}/purchase", $booking->toArray());
        $response->assertStatus(422)
            ->assertJsonFragment(['schedule_id' => ['All quotes on the schedule are sold out']]);
    }

    public function test_sale_bought_schedule()
    {
        $service      = Service::factory()->create();
        $price        = Price::factory()->create();
        $schedule     = Schedule::factory()->create([
            'service_id' => $service->id,
            'attendees'  => 1
        ]);

        $this->json('post', "api/schedules/{$schedule->id}/purchase", [
            'price_id'       => $price->id,
            'schedule_id'    => $schedule->id
        ])->assertOk();

        $this->json('post', "api/schedules/{$schedule->id}/purchase", [
            'price_id'       => $price->id,
            'schedule_id'    => $schedule->id
        ])
            ->assertStatus(422)
            ->assertJsonFragment(['schedule_id' => ['All quotes on the schedule are sold out']]);
    }
}


