<?php

namespace App\Http\Requests\Admin;

class ClientDestroyRequest extends UserDestroyRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return $this->client->isClient();
    }
}
