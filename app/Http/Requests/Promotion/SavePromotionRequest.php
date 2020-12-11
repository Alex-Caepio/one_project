<?php

namespace app\Http\Requests\Promotion;


use App\Http\Requests\Request;
use App\Models\Promotion;
use App\Models\PromotionCode;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SavePromotionRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name'            => 'required|min:5|max:255|unique:promotions,name' .
                                 ($this->promotion ? ',' . $this->promotion->id : ''),
            'valid_from'      => 'date_format:Y-m-d',
            'expiry_date'     => 'date_format:Y-m-d',
            'discount_type'   => ['required', Rule::in([Promotion::TYPE_FIXED, Promotion::TYPE_PERCENTAGE])],
            'discount_value'  => 'required|numeric',
            'spend_min'       => 'numeric',
            'spend_max'       => 'numeric',
            'service_type_id' => 'exists:service_types,id',
            'applied_to'      => ['required', Rule::in([Promotion::APPLIED_BOTH, Promotion::APPLIED_HOST])],
            'uses_per_client' => 'integer',
            'uses_per_code'   => 'integer',
            'total_codes'     => 'required|integer|min:1',
            'promocode_names' => 'array'
        ];
    }

    public function withValidator(Validator $validator) {
        $validator->after(function($validator) {
            $filledPromocodes = array_unique($this->get('promocode_names'));
            if (count($filledPromocodes)) {
                if (count($filledPromocodes) !== (int)$this->get('total_codes')) {
                    $validator->errors()
                              ->add('promocode_names', 'Please, fill all of the requested count of promocodes');
                }
                $promoCodes = PromotionCode::whereIn('name', $filledPromocodes)->pluck('name');
                if (count($promoCodes)) {
                    $validator->errors()
                              ->add('promocode_names', 'These codes are already in use: ' . implode(', ', $promoCodes));
                }
            }
        });
    }

}
