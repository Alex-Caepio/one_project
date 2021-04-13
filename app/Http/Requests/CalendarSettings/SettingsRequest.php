<?php


namespace App\Http\Requests\CalendarSettings;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class SettingsRequest extends Request {

    public function authorize() {
        return true;
    }


    public function rules(): array {
        return [
            'timezone_id' => 'required|exists:timezones,id',
        ];
    }

}
