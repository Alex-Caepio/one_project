<?php


namespace App\Transformers;

use App\Models\Notification;

class NotificationTransformer extends Transformer
{

    protected $availableIncludes = [
        'client',
        'practitioner',
        'receiver',
        'price',
        'booking'
    ];

    public function transform(Notification $notification)
    {
        return [
            'id'              => $notification->id,
            'title'           => $notification->title,
            'client_id'       => $notification->client_id,
            'practitioner_id' => $notification->practitioner_id,
            'service_id'      => $notification->service_id,
            'receiver_id'     => $notification->receiver_id,
            'old_address'     => $notification->old_address,
            'new_address'     => $notification->new_address,
            'old_datetime'    => $notification->old_datetime,
            'old_enddate'     => $notification->old_enddate,
            'new_datetime'    => $notification->new_datetime,
            'new_enddate'     => $notification->new_enddate,
            'price_id'        => $notification->price_id,
            'booking_id'      => $notification->booking_id,
            'price_payed'     => $notification->price_payed,
            'price_refunded'  => $notification->price_refunded,
            'read_at'         => $notification->read_at,
            'created_at'      => $notification->created_at,
            'updated_at'      => $notification->updated_at,
            'type'            => $notification->type,
            'datetime_from'   => $notification->datetime_from,
            'datetime_to'     => $notification->datetime_to,
        ];
    }

    public function includeClient(Notification $notification)
    {
        return $this->itemOrNull($notification->client, new UserTransformer());
    }

    public function includePractitioner(Notification $notification)
    {
        return $this->itemOrNull($notification->practitioner, new UserTransformer());
    }

    public function includeReceiver(Notification $notification)
    {
        return $this->itemOrNull($notification->receiver, new UserTransformer());
    }

    public function includePrice(Notification $notification)
    {
        return $this->itemOrNull($notification->price, new UserTransformer());
    }

    public function includeBooking(Notification $notification)
    {
        return $this->itemOrNull($notification->booking, new BookingTransformer());
    }


}
