<?php


namespace App\Http\Requests\CalendarSettings;

use App\Http\Requests\Request;

class AuthRequest extends Request {

    public function authorize() {
        return true;
    }


    public function rules(): array {
        return [
            'code' => 'required|string',
        ];
    }

}
