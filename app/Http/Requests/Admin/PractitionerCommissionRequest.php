<?php


namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

class PractitionerCommissionRequest extends Request
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
            'practitioner_id'   => 'required|exists:users,id',
            'rate' => 'min:1|max:100',
            'date_from' => 'required_if:is_dateless,false|prohibited_if:is_dateless,true',
            'date_to' => 'required_if:is_dateless,false|prohibited_if:is_dateless,true',
            'is_dateless' => 'boolean',
        ];
    }
}
