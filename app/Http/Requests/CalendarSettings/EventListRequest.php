<?php


namespace App\Http\Requests\CalendarSettings;

use App\Http\Requests\Request;
use App\Models\GoogleCalendar;
use Illuminate\Support\Facades\Auth;

class EventListRequest extends Request
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
            'first_date_point' => [
                'required',
                'date_format:Y-m-d H:i:s'
            ],
            'last_date_point' => [
                'required',
                'date_format:Y-m-d H:i:s'
            ]
        ];
    }

}
