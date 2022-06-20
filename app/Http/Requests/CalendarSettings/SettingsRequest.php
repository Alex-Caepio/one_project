<?php


namespace App\Http\Requests\CalendarSettings;

use App\Http\Requests\Request;

class SettingsRequest extends Request {

    public function authorize() {
        return true;
    }


    public function rules(): array {
        return [
            'timezone_id' => 'required|exists:timezones,id',
            'unavailabilities' => 'nullable|array'
        ];
    }

}
