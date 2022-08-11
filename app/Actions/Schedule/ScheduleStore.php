<?php

namespace App\Actions\Schedule;

use App\Http\Requests\Schedule\CreateScheduleInterface;
use App\Models\Schedule;
use App\Models\Service;
use Stripe\StripeClient;

class ScheduleStore extends ScheduleSave
{
    public function execute(CreateScheduleInterface $request, Service $service): Schedule
    {
        $data = $this->collectRequest($request, $service);
        $schedule = Schedule::create($data);
        $this->savePrices($schedule, $service, $request);
        $this->saveRelations($request, $schedule);
        return $schedule;
    }

    private function savePrices(Schedule $schedule, Service $service, CreateScheduleInterface $request): void
    {
        if ($request->filled('prices')) {
            $stripe = app(StripeClient::class);
            $data = $request->all();
            $prices = $data['prices'];
            foreach ($prices as $key => $price) {
                $stripePrice = $stripe->prices
                    ->create([
                        'unit_amount' => intval(round($prices[$key]['cost'] * 100, 0, PHP_ROUND_HALF_DOWN)),
                        'currency' => config('app.platform_currency'),
                        'product' => $service->stripe_id,
                    ]);

                $prices[$key]['stripe_id'] = $stripePrice->id;
                if ((!isset($prices[$key]['number_available']) || !$prices[$key]['number_available']) &&
                    $schedule->attendees !== null && $schedule->attendees > 0
                ) {
                    $prices[$key]['number_available'] = $schedule->attendees;
                }
                $prices[$key]['stripe_id'] = $stripePrice->id;
            }
            $schedule->prices()->createMany($prices);
        }
    }
}
