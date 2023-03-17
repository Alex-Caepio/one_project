<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'sent_at' => $this->created_at,
            'sender' => new UserResource($this->sender),
            'receiver' => new UserResource($this->receiver)
        ];
    }
}
