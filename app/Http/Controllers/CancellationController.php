<?php

namespace App\Http\Controllers;


use App\Actions\Cancellation\CancelBooking;
use App\Http\Requests\Cancellation\CancelBookingRequest;
use App\Http\Requests\Request;
use App\Models\Booking;
use App\Models\Cancellation;
use App\Transformers\CancellationTransformer;
use Illuminate\Support\Facades\Auth;

class CancellationController extends Controller {

    // get list of cancellation
    public function index(Request $request) {
        $queryCancellation = Cancellation::query();
        $fieldName = Auth::user()->isPractitioner() ? 'practitioner_id' : 'user_id';
        $queryCancellation->where($fieldName, Auth::id());

        if ($request->filled('booking_id')) {
            $queryCancellation->where('booking_id', '=', (int)$request->get('booking_id'));
        }

        $includes = $request->getIncludes();
        if (count($includes)) {
            $queryCancellation->with($includes);
        }
        return response(fractal($queryCancellation->get(),new CancellationTransformer())
                            ->parseIncludes($includes));
    }

    public function cancelBooking(Booking $booking, CancelBookingRequest $request) {
        return run_action(CancelBooking::class, $booking);

    }

}
