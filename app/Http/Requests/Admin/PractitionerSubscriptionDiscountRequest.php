<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;
use App\Models\PractitionerSubscriptionDiscount;
use App\Models\User;
use Illuminate\Validation\Rule;

/**
 * @property-read int $user_id
 * @property-read int $rate
 * @property-read string $duration_type
 * @property-read int|null $duration_in_months
 */
class PractitionerSubscriptionDiscountRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                Rule::exists('users', 'id')
                    ->where('account_type', User::ACCOUNT_PRACTITIONER)
                    ->where('is_published', true)
            ],
            'rate' => 'required|min:1|max:1000000',
            'duration_type' => [
                'required',
                Rule::in(
                    PractitionerSubscriptionDiscount::REPEATING_SUBSCRIPTION_TYPE,
                    PractitionerSubscriptionDiscount::FOREVER_SUBCRIPTION_TYPE,
                )
            ],
            'duration_in_months' => [
                'required_if:duration_type,'.PractitionerSubscriptionDiscount::REPEATING_SUBSCRIPTION_TYPE,
                'min:1|max:100000',
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = User::where('id', $this->get('user_id'))->with('plan')->first();
            if (!$user->plan) {
                $validator->errors()->add('user_id', 'Subscription plan must be attached to the practitioner');
            }
        });
    }

    public function getUser(): User
    {
        return User::find($this->user_id);
    }
}
