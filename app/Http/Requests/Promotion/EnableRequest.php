<?php

namespace App\Http\Requests\Promotion;

use App\Http\Requests\Request;
use Carbon\Carbon;
use Illuminate\Validation\Validator;

class EnableRequest extends Request {
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
        return [];
    }

    public function withValidator(Validator $validator): void {
        $validator->after(function($validator) {
            $this->validatePromo($validator);
        });
    }

    public function validatePromo(Validator $validator): void {
        if (!empty($this->promotion->expiry_date)) {
            if (Carbon::parse($this->promotion->expiry_date) <= Carbon::now()) {
                $validator->errors()->add('promotion', 'Promotion is ended');
            }
        }
    }
}
