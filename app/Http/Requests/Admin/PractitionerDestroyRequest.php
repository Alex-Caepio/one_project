<?php

namespace App\Http\Requests\Admin;

class PractitionerDestroyRequest extends UserDestroyRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->practitioner->isPractitioner();
    }
}
