<?php

namespace App\Http\Requests\Schedule;

use App\Http\Requests\Request;

/**
 * @property string $payment_intent_id
 */
class PurchaseFinalizeRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules() {
        return [
            'payment_intent_id' => 'required|string|min:4',
        ];
    }
}
