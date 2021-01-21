<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

class PlanStoreRequest extends Request
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
            'name'   => 'required|max:20',
            'price' => 'required_unless:is_free,true|nullable|integer|min:0',
            'description' => 'max:150',
            'trial_months' => 'nullable|integer',
            'commission_on_sale' => 'min:0|max:100',
            'schedules_per_service' => 'min:1|max:20',
            'pricing_options_per_service' => 'min:1|max:20',
            'article_publishing' => 'min:1|max:20'
        ];
    }

}
