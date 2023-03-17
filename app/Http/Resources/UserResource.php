<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->account_type,
            'name' => $this->isPractitioner()
                ? $this->business_name
                : $this->first_name . ' ' . $this->last_name
        ];
    }
}
