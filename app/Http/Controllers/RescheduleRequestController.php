<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reschedule\RescheduleRequestRequest;
use App\Models\Booking;
use App\Http\Requests\Request;
use App\Models\RescheduleRequest;
use App\Actions\RescheduleRequest\RescheduleRequestStore;
use App\Transformers\RescheduleRequestTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RescheduleRequestController extends Controller {
    public function index(Request $request) {
        $includes = $request->getIncludes();
        $paginator = RescheduleRequest::where('user_id', Auth::id())->with($includes)->paginate($request->getLimit());

        return fractal($paginator->getCollection(), new RescheduleRequestTransformer())
            ->parseIncludes($request->getIncludes())->toArray();
    }

    public function inbound(Request $request) {
        $includes = $request->getIncludes();

        $paginator =
            RescheduleRequest::where('user_id', Auth::id())->where('requested_by', 'practitioner')->with($includes)
                             ->paginate($request->getLimit());

        return fractal($paginator->getCollection(), new RescheduleRequestTransformer())->parseIncludes($includes)
                                                                                       ->toArray();
    }

    public function outbound(Request $request) {
        $includes = $request->getIncludes();
        $paginator = RescheduleRequest::where('user_id', Auth::id())->where('requested_by', 'client')->with($includes)
                                      ->paginate($request->getLimit());

        return fractal($paginator->getCollection(), new RescheduleRequestTransformer())->parseIncludes($includes)
                                                                                       ->toArray();
    }

    public function reschedule(RescheduleRequestRequest $request, Booking $booking) {
        RescheduleRequest::where('booking_id', $booking->id)->delete();
        run_action(RescheduleRequestStore::class, $booking, $request);
        return response(null, 200);
    }

    public function allReschedule(RescheduleRequestRequest $request) {
        RescheduleRequest::whereIn('booking_id', $request->booking_ids)->delete();
        $bookings = Booking::whereIn('id', $request->booking_ids)->get();
        foreach ($bookings as $bookingItem) {
            run_action(RescheduleRequestStore::class, $bookingItem, $request);
        }
        return response(null, 200);
    }

    public function accept(RescheduleRequest $rescheduleRequest) {
        $booking = $rescheduleRequest->booking;
        $booking->schedule_id = $rescheduleRequest->new_schedule_id;
        $booking->datetime_from = $rescheduleRequest->new_start_date;
        $booking->datetime_to = $rescheduleRequest->new_end_date;

        $booking->update();
        $rescheduleRequest->delete();

        return response(null, 204);
    }

    public function decline(RescheduleRequest $rescheduleRequest) {
        $rescheduleRequest->delete();
        return response(null, 204);
    }
}
