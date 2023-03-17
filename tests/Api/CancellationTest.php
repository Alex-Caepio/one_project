<?php

namespace Tests\Api;

use App\Models\Booking;
use App\Models\Plan;
use App\Models\Price;
use App\Models\Purchase;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Stripe\StripeClient;
use Tests\TestCase;
use Tests\Traits\UsesStripe;

class CancellationTest extends TestCase
{
    use DatabaseTransactions, UsesStripe;

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

    public function test_user_can_cancell_installment(): void
    {
        //practitioner
        $plan         = Plan::factory()->create(['commission_on_sale' => 10]);
        $practitioner = User::factory()->create(['plan_id' => $plan->id]);
        $this->createConnectAccount($practitioner);

        //customer
        $customer      = $this->createStripeClient($this->user);
        $paymentMethod = $this->createStripePaymentMethod($this->user);

        //product
        $stripeProduct = $this->creteStripeProduct();
        $service       = Service::factory()->create([
            'service_type_id' => 'workshop',
            'stripe_id'       => $stripeProduct->id,
            'user_id'         => $practitioner->id
        ]);
        $schedule      = Schedule::factory()->create([
            'service_id'          => $service->id,
            'deposit_accepted'    => true,
            'deposit_amount'      => 10,
            'deposit_instalments' => 4,
            'deposit_final_date' => Carbon::now()->addMonth()->toDateTimeString()
        ]);
        $price         = Price::factory()->create([
            'schedule_id' => $schedule->id,
            'stripe_id'   => $stripeProduct->id,
            'is_free'     => false,
            'cost'        => 100
        ]);
        $purchase = Purchase::factory()->create([
            'user_id' => $this->user->id,
            'is_deposit' => true,
        ]);
        $booking = Booking::factory()->create([
            'practitioner_id' => $this->user->id,
            'user_id' => $this->user->id,
            'schedule_id' => $schedule->id,
            'price_id' => $price->id,
            'purchase_id' => $purchase->id,
            'is_installment' => true
        ]);
        $response = $this->json('post', '/api/cancellations/bookings', [
            'booking_ids' => [$booking->id]
        ]);

        $response->assertStatus(204);
    }

    //todo: might want to move it to the UsesStripe trait
    protected function creteStripeProduct()
    {
        $client = app()->make(StripeClient::class);
        return $client->products->create(['name' => 'Test product @' . now()->toDateTimeString()]);
    }
}
