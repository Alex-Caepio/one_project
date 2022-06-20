<?php

namespace App\Http\Requests\Cancellation;

use App\Models\Booking;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property Booking booking
 */
class CancelManyBookingsRequest extends FormRequest
{
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
    public function rules()
    {
        return [];
    }


}
