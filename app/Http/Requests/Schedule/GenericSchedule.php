<?php

namespace App\Http\Requests\Schedule;

use App\Http\Requests\Request;
use App\Traits\ScheduleValidator;
use Illuminate\Support\Facades\Log;

class GenericSchedule extends Request implements CreateScheduleInterface {

    use ScheduleValidator;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return $this->user()->onlyUnpublishedAllowed();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [];
    }

    public function withValidator($validator): void {
        $validator->after(function($validator) {
            $this->userScheduleValidator($validator, $this->service);
        });
    }

}
