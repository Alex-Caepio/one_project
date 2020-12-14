<?php

namespace App\Http\Requests\Reschedule;

use App\Models\Schedule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RescheduleRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return  $this->booking->user_id == Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'new_schedule_id' => 'required|exists:schedules,id',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator)
        {
        if($this->booking->schedule->service->id != Schedule::find($this->get('new_schedule_id'))->service->id){
                $validator->errors()->add('new_schedule_id', 'This schedule does not belong to the service.');
            }
        });
    }
}
