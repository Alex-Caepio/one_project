<?php


namespace App\Transformers;


use App\Models\Schedule;

class ScheduleTransformer extends Transformer
{
    protected $availableIncludes = [
        'location', 'prices', 'service', 'users', 'media_files',
        'schedule_availabilities', 'schedule_unavailabilities',
        'schedule_files', 'schedule_hidden_files'
    ];

    public function transform(Schedule $schedule)
    {
        return [
            'id'                            => $schedule->id,
            'title'                         => $schedule->title,
            'location_id'                   => $schedule->location_id,
            'service_id'                    => $schedule->service_id,
            'start_date'                    => $schedule->start_date,
            'end_date'                      => $schedule->end_date,
            'attendees'                     => $schedule->attendees,
            'cost'                          => $schedule->cost,
            'comments'                      => $schedule->comments,
            'city'                          => $schedule->city,
            'country'                       => $schedule->country,
            'post_code'                     => $schedule->post_code,
            'location_displayed'            => $schedule->location_displayed,
            'meals_breakfast'               => (bool) $schedule['meals_breakfast'] ?: false,
            'meals_lunch'                   => (bool) $schedule['meals_lunch'] ?: false,
            'meals_dinner'                  => (bool) $schedule['meals_dinner'] ?: false,
            'meals_alcoholic_beverages'     => (bool) $schedule['meals_alcoholic_beverages'] ?: false,
            'meals_dietry_accomodated'      => (bool) $schedule['meals_dietry_accomodated'] ?: false,
            'refund_terms'                  => $schedule->refund_terms,
            'deposit_accepted'              => $schedule->deposit_accepted,
            'deposit_amount'                => $schedule->deposit_amount,
            'deposit_instalments'           => $schedule->deposit_instalments,
            'deposit_instalment_frequency'  => $schedule->deposit_instalment_frequency,
            'deposit_final_date'            => $schedule->deposit_final_date,
            'booking_message'               => $schedule->booking_message,
            'url'                           => $schedule->url,
            'book_full_series'              => $schedule->book_full_series,
            'accomodation'                  => $schedule->accomodation,
            'accomodation_details'          => $schedule->accomodation_details,
            'travel'                        => $schedule->travel,
            'travel_details'                => $schedule->travel_details,
            'repeat'                        => $schedule->repeat,
            'repeat_every'                  => $schedule->repeat_every,
            'repeat_period'                 => $schedule->repeat_period,
            'notice_min_time'               => $schedule->notice_min_time,
            'notice_min_period'             => $schedule->notice_min_period,
            'buffer_time'                   => $schedule->buffer_time,
            'buffer_period'                 => $schedule->bubuffer_periodfbuffer_periodfer_period,
            'address'                       => $schedule->address,
            'appointment'                   => $schedule->appointment,
            'created_at'                    => $schedule->created_at,
            'updated_at'                    => $schedule->updated_at,
            'venue_name'                    => $schedule->venue_name,
            'venue_address'                 => $schedule->venue_address,
            'within_kilometers'             => $schedule->within_kilometers,
            'deleted_at'                    => $schedule->deleted_at,
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
        return $this->collectionOrNull($schedule->schedule_availabilities, new ScheduleAvailabilityTransformer());
    }

    public function includeScheduleUnavailabilities(Schedule $schedule)
    {
        return $this->collectionOrNull($schedule->schedule_unavailabilities, new ScheduleUnavailabilityTransformer());
    }

    public function includeScheduleFiles(Schedule $schedule)
    {
        return $this->collectionOrNull($schedule->schedule_files, new ScheduleFileTransformer());
    }
    public function includeScheduleHiddenFiles(Schedule $schedule)
    {
        return $this->collectionOrNull($schedule->schedule_hidden_files, new ScheduleHiddenFileTransformer());
    }
}
