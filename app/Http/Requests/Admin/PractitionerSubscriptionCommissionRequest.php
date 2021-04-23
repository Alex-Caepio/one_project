<?php


namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

class PractitionerSubscriptionCommissionRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id'   => 'required|exists:users,id',
            'rate' => 'required',
            'date_from' => 'required_if:is_dateless,false',
            'date_to' => 'required_if:is_dateless,false',
        ];
    }
}
