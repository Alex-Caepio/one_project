<?php

namespace App\Actions\Schedule;

use App\Models\Price;
use App\Models\Service;
use App\Models\Schedule;
use Stripe\StripeClient;
use Illuminate\Database\Eloquent\Collection;

class HandlePricesUpdate
{
    /**
     * @var mixed|StripeClient
     */
    public $stripe;
    /**
     * @var Schedule
     */
    public $schedule;
    /**
     * @var Service
     */
    public $service;

    public function execute(iterable $prices, Schedule $schedule)
    {
        $this->stripe   = app()->make(StripeClient::class);
        $this->schedule = $schedule;
        $this->service  = $schedule->service;

        if ($pricesToDelete = $this->fetchPricesToDelete($prices)) {
            $this->deletePrices($pricesToDelete);
        }
        if ($pricesToCreate = $this->filterPricesToCreate($prices)) {
            $this->createPrices($pricesToCreate);
        }
        if ($pricesToUpdate = $this->filterPricesToUpdate($prices)) {
            $this->updatePrices($pricesToUpdate);
        }
    }

    protected function filterPricesToCreate(iterable $prices): \Illuminate\Support\Collection
    {
        $pricesFiltered = [];
        foreach ($prices as $price) {
            if (empty($price['id'])) {
                $pricesFiltered[] = $price;
            }
        }

        return collect($pricesFiltered);
    }

    protected function filterPricesToUpdate(iterable $prices): \Illuminate\Support\Collection
    {
        $pricesFiltered = [];
        foreach ($prices as $price) {
            if (!empty($price['id'])) {
                $pricesFiltered[] = $price;
            }
        }

        return collect($pricesFiltered);
    }

    protected function fetchPricesToDelete(iterable $prices): Collection
    {
        $idsExisting = $this->schedule->prices()->pluck('id')->toArray();
        $idsEditing  = $this->filterPricesToUpdate($prices)->pluck('id')->toArray();
        $idsDeleting = array_diff($idsExisting, $idsEditing);

        return $this->schedule
            ->prices()
            ->whereIn('id', $idsDeleting)
            ->get();
    }

    protected function deletePrices(iterable $prices): void
    {
        //deactivate stripe price
        foreach ($prices as $price) {
            $this->stripe->prices->update($price->stripe_id, ['active' => false]);
        }

        $this->schedule
            ->prices()
            ->whereIn('id', $prices->pluck('id'))
            ->delete();
    }

    protected function updatePrices(iterable $prices): void
    {
        $pricesToUpdate = Price::whereIn('id', $prices->pluck('id'))->get()->keyBy('id');

        foreach ($prices as $price) {
            $this->stripe->prices->update($pricesToUpdate[$price['id']]->stripe_id,['active' => false]);
            $stripePrice        = $this->stripe->prices->create([
                'unit_amount' => $price['cost'] ?? 0,
                'currency'    => config('app.platform_currency'),
                'product'     => $this->service->stripe_id,
            ]);

            $price['stripe_id'] = $stripePrice->id;
            $price['cost']      = $price['cost'] ?? 0;

            Price::where('id', $price['id'])->update($price);
        }
    }

    protected function createPrices(iterable $prices): void
    {
        $pricesToCreate = $prices->map(function ($price) {
            $stripePrice        = $this->stripe->prices->create([
                'unit_amount' => $price['cost'] ?? 0,
                'currency'    => config('app.platform_currency'),
                'product'     => $this->service->stripe_id,
            ]);
            $price['stripe_id'] = $stripePrice->id;
            $price['cost']      = $price['cost'] ?? 0;

            return $price;
        });

        $this->schedule->prices()->createMany($pricesToCreate);
    }
}
