<?php


namespace App\Transformers;

use App\Models\Notification;

class NotificationTransformer extends Transformer
{

    public function transform(Notification $notification)
    {
        return [
            'id'                => $notification->id,
            'title'             => $notification->title,
            'client_id'         => $notification->client_id,
            'practitioner_id'   => $notification->practitioner_id,
            'receiver_id'       => $notification->receiver_id,
            'old_address'       => $notification->old_address,
            'new_address'       => $notification->new_address,
            'old_datetime'      => $notification->old_datetime,
            'new_datetime'      => $notification->new_datetime,
            'price_id'          => $notification->price_id,
            'price_payed'       => $notification->price_payed,
            'price_refunded'    => $notification->price_refunded,
            'read_at'           => $notification->read_at,
            'created_at'        => $notification->created_at,
            'updated_at'        => $notification->updated_at,
            'type'              => $notification->type,
        ];
    }

}
