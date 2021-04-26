<?php


namespace App\Http\Requests\CalendarSettings;

use App\Http\Requests\Request;
use App\Models\GoogleCalendar;
use Illuminate\Support\Facades\Auth;

class EventListRequest extends Request {

    public function authorize() {
        return Auth::user() && Auth::user()->calendar instanceof GoogleCalendar && Auth::user()->calendar->access_token;
    }


    public function rules(): array {
        return [];
    }

}
