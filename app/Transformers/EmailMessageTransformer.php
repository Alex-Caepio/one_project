<?php


namespace App\Transformers;


use App\Models\EmailMessage;
use App\Models\User;

class EmailMessageTransformer extends Transformer
{
    protected $availableIncludes = ['receiver','sender'];

    public function transform(EmailMessage $emailMessage): array
    {
        return [
            'id' => $emailMessage->id,
            'sender_id' => $emailMessage->sender_id,
            'receiver_id' => $emailMessage->receiver_id,
            'text' => $emailMessage->text,
            'created_at' => $emailMessage->created_at,
            'updated_at' => $emailMessage->updated_at,
        ];
    }

    public function includeReceiver(EmailMessage $emailMessage)
    {
        return $this->itemOrNull($emailMessage->receiver, new UserTransformer());
    }

    public function includeSender(EmailMessage $emailMessage)
    {
        return $this->itemOrNull($emailMessage->sender, new UserTransformer());
    }
}
