<?php


namespace App\Transformers;


use App\Models\Schedule;

class ScheduleTransformer extends Transformer
{
    protected $availableIncludes = ['location', 'prices', 'service', 'users'];

    public function transform(Schedule $schedule)
    {
        return [
            'id' => $schedule->id,
            'title' => $schedule->title,
            'service_id' => $schedule->service_id,
            'start_date' => $schedule->start_date,
            'end_date' => $schedule->end_date,
            'cost' => $schedule->cost,
            'created_at' => $schedule->created_at,
            'updated_at' => $schedule->updated_at,
        ];
    }

    public function includeLocation(Schedule $schedule)
    {
        return $this->itemOrNull($schedule->location, new LocationTransformer());
    }

    public function includePrices(Schedule $schedule)
    {
        return $this->collectionOrNull($schedule->prices, new PriceTransformer());
    }

    public function includeService(Schedule $schedule)
    {
        return $this->itemOrNull($schedule->service, new ServiceTransformer());
    }

    public function includeUsers(Schedule $schedule)
    {
        return $this->collectionOrNull($schedule->users, new UserTransformer());
    }
}
