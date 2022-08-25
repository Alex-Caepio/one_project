<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @property-read bool $cancel_bookings
 */
class UnpublishPractitionerRequest extends Request
{
    public function authorize(): bool
    {
        return Auth::user()->isPractitioner() && Auth::user()->is_published;
    }

    public function rules(): array
    {
        return [
            'cancel_bookings' => 'required|bool',
        ];
    }
}
