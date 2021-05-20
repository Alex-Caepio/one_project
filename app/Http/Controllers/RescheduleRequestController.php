<?php

namespace App\Http\Controllers;

use App\Actions\Cancellation\CancelBooking;
use App\Actions\RescheduleRequest\RescheduleRequestAccept;
use App\Actions\RescheduleRequest\RescheduleRequestDecline;
use App\Events\BookingRescheduleAcceptedByClient;
use App\Events\RescheduleRequestDeclinedByClient;
use App\Http\Requests\Reschedule\RescheduleRequestRequest;
use App\Http\Requests\Reschedule\ScheduleRescheduleRequestRequest;
use App\Models\Booking;
use App\Http\Requests\Reschedule\AcceptRescheduleRequestRequest;
use App\Http\Requests\Reschedule\DeclineRescheduleRequestRequest;
use App\Http\Requests\Request;
use App\Models\RescheduleRequest;
use App\Actions\RescheduleRequest\RescheduleRequestStore;
use App\Models\Schedule;
use App\Models\User;
use App\Transformers\RescheduleRequestTransformer;
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
        return response(null, 204);
    }

    public function allReschedule(RescheduleRequestRequest $request) {
        $this->rescheduleByBookingIds($request->booking_ids, $request);
        return response(null, 204);
    }

    public function scheduleReschedule(Schedule $schedule, ScheduleRescheduleRequestRequest $request) {
        $bookings = $schedule->bookings()->active()->get();
        if (count($bookings)) {
            $this->rescheduleByBookingIds($bookings->pluck('id')->all(), $request);
        }
        return response(null, 204);
    }

    private function rescheduleByBookingIds(array $bookingIds, $request): void {
        RescheduleRequest::whereIn('booking_id', $bookingIds)->delete();
        $bookings = Booking::whereIn('id', $bookingIds)->active()->get();
        foreach ($bookings as $bookingItem) {
            run_action(RescheduleRequestStore::class, $bookingItem, $request);
        }
    }

    public function accept(AcceptRescheduleRequestRequest $request, RescheduleRequest $rescheduleRequest) {
        run_action(RescheduleRequestAccept::class, $rescheduleRequest);
        return response(null, 204);
    }

    public function decline(DeclineRescheduleRequestRequest $request, RescheduleRequest $rescheduleRequest) {
        run_action(RescheduleRequestDecline::class, $rescheduleRequest);
        return response(null, 204);
    }
}
