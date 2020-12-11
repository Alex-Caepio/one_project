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
            'id'                => $rescheduleRequest->id,
            'schedule_id'       => $rescheduleRequest->schedule_id,
            'user_id'           => $rescheduleRequest->user_id,
            'new_schedule_id'   => $rescheduleRequest->new_schedule_id,
            'booking_id'        => $rescheduleRequest->booking_id,
            'new_datetime_from' => $rescheduleRequest->new_datetime_from,
            'new_price_id'      => $rescheduleRequest->new_price_id,
            'comment'           => $rescheduleRequest->comment,

        ];
    }

    public function includeUsers(RescheduleRequest $rescheduleRequest)
    {
        return $this->collectionOrNull($rescheduleRequest->user, new UserTransformer());
    }

    public function includeBookings(RescheduleRequest $rescheduleRequest)
    {
        return $this->collectionOrNull($rescheduleRequest->booking, new BookingTransformer());
    }

    public function includeOldSchedules(RescheduleRequest $rescheduleRequest)
    {
        return $this->collectionOrNull($rescheduleRequest->old_schedule, new ScheduleTransformer());
    }

    public function includeNewSchedules(RescheduleRequest $rescheduleRequest)
    {
        return $this->collectionOrNull($rescheduleRequest->new_schedule, new ScheduleTransformer());
    }
}
