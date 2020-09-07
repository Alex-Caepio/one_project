<?php

namespace App\Http\Controllers;

use App\Actions\RescheduleRequest\RescheduleRequestStore;
use App\Http\Requests\Request;
use App\Models\RescheduleRequest;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class RescheduleRequestController extends Controller
{
    public function index()
    {
        $user = Auth::id();
        $rescheduleRequest = RescheduleRequest::all()->where('user_id', $user);
        return response($rescheduleRequest);
    }

    public function store(Schedule $schedule, Request $request)
    {
        run_action(RescheduleRequestStore::class, $schedule, $request);
    }

    public function accept(RescheduleRequest $rescheduleRequest)
    {
        Auth::user()->schedules()->detach($rescheduleRequest->schedule_id);
        Auth::user()->schedules()->attach($rescheduleRequest->new_schedule_id);
    }

    public function decline(RescheduleRequest $rescheduleRequest)
    {
        $rescheduleRequest->delete();
        return response(null, 204);
    }
}
