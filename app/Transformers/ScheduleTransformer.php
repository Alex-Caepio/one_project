<?php


namespace App\Transformers;


use App\Models\Schedule;

class ScheduleTransformer extends Transformer
{
    protected $availableIncludes = [
        'location', 'prices', 'service', 'users', 'media_files',
        'schedule_availabilities', 'schedule_unavailabilities'
    ];

    public function transform(Schedule $schedule)
    {
        return [
            'id'                        => $schedule->id,
            'title'                     => $schedule->title,
            'service_id'                => $schedule->service_id,
            'start_date'                => $schedule->start_date,
            'end_date'                  => $schedule->end_date,
            'cost'                      => $schedule->cost,
            'meals_breakfast'           => $schedule->meals_breakfast,
            'meals_lunch'               => $schedule->meals_lunch,
            'meals_dinner'              => $schedule->meals_dinner,
            'meals_alcoholic_beverages' => $schedule->meals_alcoholic_beverages,
            'meals_dietry_accomodated'  => $schedule->meals_dietry_accomodated,
            'refund_terms'              => $schedule->refund_terms,
            'deposit_accepted'          => $schedule->deposit_accepted,
            'deposit_amount'            => $schedule->deposit_amount,
            'deposit_final_date'        => $schedule->deposit_final_date,
            'booking_message'           => $schedule->booking_message,
            'repeat'                    => $schedule->repeat,
            'repeat_every'              => $schedule->repeat_every,
            'repeat_period'             => $schedule->repeat_period,
            'notice_min_time'           => $schedule->notice_min_time,
            'notice_min_period'         => $schedule->notice_min_period,
            'buffer_time'               => $schedule->buffer_time,
            'buffer_period'             => $schedule->bubuffer_periodfbuffer_periodfer_period,
            'created_at'                => $schedule->created_at,
            'updated_at'                => $schedule->updated_at,
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

    public function includeMediaFiles(Schedule $schedule)
    {
        return $this->collectionOrNull($schedule->media_files, new MediaFileTransformer());
    }

    public function includeScheduleAvailabilities(Schedule $schedule)
    {
        return $this->collectionOrNull($schedule->schedule_availabilities, new ScheduleAvailabilitiesTransformer());
    }

    public function includeScheduleUnavailabilities(Schedule $schedule)
    {
        return $this->collectionOrNull($schedule->schedule_unavailabilities, new ScheduleUnavailabilitiesTransformer());
    }
}
