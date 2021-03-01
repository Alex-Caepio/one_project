<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reschedule\AcceptRescheduleRequestRequest;
use App\Http\Requests\Reschedule\DeclineRescheduleRequestRequest;
use App\Models\Schedule;
use App\Http\Requests\Request;
use App\Models\RescheduleRequest;
use App\Actions\RescheduleRequest\RescheduleRequestStore;
use App\Transformers\RescheduleRequestTransformer;
use Illuminate\Support\Facades\Auth;

class RescheduleRequestController extends Controller
{
    public function index(Request $request)
    {
        $includes = $request->getIncludes();
        $paginator = RescheduleRequest::where('user_id', Auth::id())
            ->with($includes)
            ->paginate($request->getLimit());

        return fractal($paginator->getCollection(), new RescheduleRequestTransformer())
            ->parseIncludes($request->getIncludes())->toArray();
    }

    public function inbound(Request $request)
    {
        $includes = $request->getIncludes();

        $paginator = RescheduleRequest::where('user_id', Auth::id())
            ->where('requested_by', 'practitioner')
            ->with($includes)
            ->paginate($request->getLimit());

        return fractal($paginator->getCollection(), new RescheduleRequestTransformer())
            ->parseIncludes($includes)
            ->toArray();
    }

    public function outbound(Request $request)
    {
        $includes = $request->getIncludes();
        $paginator = RescheduleRequest::where('user_id', Auth::id())
            ->where('requested_by', 'client')
            ->with($includes)
            ->paginate($request->getLimit());

        return fractal($paginator->getCollection(), new RescheduleRequestTransformer())
            ->parseIncludes($includes)
            ->toArray();
    }

    public function store(Schedule $schedule, Request $request)
    {
        $user = Auth::id();
        RescheduleRequest::where('schedule_id', $schedule->id)->where('user_id', $user)->delete();

        run_action(RescheduleRequestStore::class, $schedule, $request);
    }

    public function accept(AcceptRescheduleRequestRequest $request, RescheduleRequest $rescheduleRequest)
    {
        $booking = $rescheduleRequest->booking;
        $booking->schedule_id = $rescheduleRequest->new_schedule_id;
        $booking->datetime_from = $rescheduleRequest->new_start_date;
        $booking->datetime_to = $rescheduleRequest->new_end_date;

        $booking->update();
        $rescheduleRequest->delete();

        return response(null, 204);
    }

    public function decline(DeclineRescheduleRequestRequest $request, RescheduleRequest $rescheduleRequest)
    {
        $rescheduleRequest->delete();
        return response(null, 204);
    }
}
