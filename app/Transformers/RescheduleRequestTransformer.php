<?php


namespace App\Transformers;

use App\Models\RescheduleRequest;

class RescheduleRequestTransformer extends Transformer
{
    protected $availableIncludes = [
        'user', 'booking', 'old_schedule', 'new_schedule'
        ];

    public function transform(RescheduleRequest $rescheduleRequest)
    {
        return [
            'id'                     => $rescheduleRequest->id,
            'schedule_id'            => $rescheduleRequest->schedule_id,
            'user_id'                => $rescheduleRequest->user_id,
            'new_schedule_id'        => $rescheduleRequest->new_schedule_id,
            'booking_id'             => $rescheduleRequest->booking_id,
            'new_price_id'           => $rescheduleRequest->new_price_id,
            'comment'                => $rescheduleRequest->comment,
            'old_location_displayed' => $rescheduleRequest->old_location_displayed,
            'new_location_displayed' => $rescheduleRequest->new_location_displayed,
            'old_start_date'         => $rescheduleRequest->old_start_date,
            'new_start_date'         => $rescheduleRequest->new_start_date,
            'old_end_date'           => $rescheduleRequest->old_end_date,
            'new_end_date'           => $rescheduleRequest->new_end_date,
            'created_at'             => $rescheduleRequest->created_at,
            'updated_at'             => $rescheduleRequest->updated_at,
            'old_price_id'           => $rescheduleRequest->old_price_id,
            'requested_by'           => $rescheduleRequest->requested_by,
        ];
    }

    public function includeUsers(RescheduleRequest $rescheduleRequest)
    {
        return $this->collectionOrNull($rescheduleRequest->user, new UserTransformer());
    }

    public function includeBooking(RescheduleRequest $rescheduleRequest)
    {
        return $this->collectionOrNull($rescheduleRequest->booking, new BookingTransformer());
    }

    public function includeOldSchedule(RescheduleRequest $rescheduleRequest)
    {
        return $this->collectionOrNull($rescheduleRequest->old_schedule, new ScheduleTransformer());
    }

    public function includeNewSchedule(RescheduleRequest $rescheduleRequest)
    {
        return $this->collectionOrNull($rescheduleRequest->new_schedule, new ScheduleTransformer());
    }
}
