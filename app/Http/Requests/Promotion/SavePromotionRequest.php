<?php

namespace App\Http\Requests\Promotion;


use App\Http\Requests\Request;
use App\Models\Promotion;
use App\Models\PromotionCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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
            'name'            => 'required|min:3|max:80|unique:promotions,name' .
                                 ($this->promotion ? ',' . $this->promotion->id : ''),
            'valid_from'      => 'date_format:Y-m-d',
            'expiry_date'     => 'nullable|date_format:Y-m-d',
            'discount_type'   => ['required', Rule::in([Promotion::TYPE_FIXED, Promotion::TYPE_PERCENTAGE])],
            'discount_value'  => 'required|numeric',
            'spend_min'       => 'nullable|numeric|min:0|max:10000',
            'spend_max'       => 'nullable|numeric|min:0|max:10000',
            'applied_to'      => ['required', Rule::in([Promotion::APPLIED_BOTH, Promotion::APPLIED_HOST])],
            'uses_per_client' => 'nullable|integer',
            'uses_per_code'   => 'nullable|integer',
            'total_codes'     => (!$this->promotion ? 'required|' : '') . 'integer|min:1',
            'promocode_names' => 'array'
        ];
    }

    public function withValidator(Validator $validator) {
        $validator->after(function($validator) {
            $spendMax = $this->get('spend_max', 0);
            $spendMin = $this->get('spend_min', 0);
            if ($spendMax > 0 && $spendMin > $spendMax) {
                $validator->errors()->add('spend_max', 'The value must be greater than spend min');
            }

            $discountType = $this->get('discount_type');
            $discountValue = $this->get('discount_value');
            if ($discountType === Promotion::TYPE_PERCENTAGE && $discountValue > 100) {
                $validator->errors()
                          ->add('discount_value', 'For percentage discount max value can be lower or equal 100');
            }

            $filledPromocodes = array_unique($this->get('promocode_names', []));
            if (count($filledPromocodes)) {
                if (count($filledPromocodes) !== (int)$this->get('total_codes')) {
                    $validator->errors()
                              ->add('promocode_names', 'Please, fill all of the requested count of promocodes');
                }
                $promoCodes =
                    PromotionCode::withTrashed()
                                 ->whereIn('name', $filledPromocodes)
                                 ->pluck('name')
                                 ->toArray();
                if (count($promoCodes)) {
                    $validator->errors()->add('promocode_names',
                                              'These codes are already in use: '
                                              . (implode(', ', $promoCodes)));
                }
            }
        });
    }

}
