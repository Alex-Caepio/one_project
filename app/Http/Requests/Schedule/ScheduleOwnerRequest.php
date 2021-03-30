<?php


namespace App\Http\Requests\Schedule;


use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleOwnerRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return $this->schedule->service->user_id === Auth::id();
    }

}
