<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class PractitionerPublishRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->practitioner->isPractitioner();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
