<?php

namespace App\Http\Requests\Admin;

class PractitionerDestroyRequest extends UserDestroyRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return $this->practitioner->isPractitioner();
    }

}
