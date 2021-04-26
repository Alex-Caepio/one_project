<?php


namespace App\Http\Requests\CalendarSettings;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

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
