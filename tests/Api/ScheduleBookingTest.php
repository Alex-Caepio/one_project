<?php

namespace Tests\Api;

use App\Models\Booking;
use App\Models\Instalment;
use App\Models\Price;
use App\Models\Purchase;
use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use phpDocumentor\Reflection\Location;
use Tests\TestCase;

class ScheduleBookingTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_user_can_only_see_upcoming_bookings_for_schedule(): void
    {
        Event::fake();
        $serviceType = ServiceType::factory()->create();
        $client      = User::factory()->create();
        $service     = Service::factory()->create(['user_id' => $this->user->id, 'service_type_id' => $serviceType->id]);
        $schedule    = Schedule::factory()->create(['service_id' => $service->id]);
        $price       = Price::factory()->create(['schedule_id' => $schedule->id, 'number_available' => 20]);
        Booking::factory()->count(3)->create(['user_id' => $client->id, 'schedule_id' => $schedule->id, 'datetime_from' => Carbon::now()->addDays(1), 'price_id' => $price->id, 'is_installment' => true]);
        Booking::factory()->count(3)->create(['user_id' => $client->id, 'schedule_id' => $schedule->id, 'datetime_from' => Carbon::now()->subDays(1), 'price_id' => $price->id]);

        $response = $this->json('get', "/api/schedules/{$schedule->id}/upcoming-bookings");
        $response->assertOk();
        $response->assertJsonCount(3);
    }

}
