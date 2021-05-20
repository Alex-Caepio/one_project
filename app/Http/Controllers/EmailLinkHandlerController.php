<?php

namespace App\Http\Controllers;

use App\Actions\RescheduleRequest\RescheduleRequestAccept;
use App\Actions\RescheduleRequest\RescheduleRequestDecline;
use App\Http\Requests\Reschedule\AcceptRescheduleRequestEmailRequest;
use App\Http\Requests\Reschedule\DeclineRescheduleRequestEmailRequest;
use App\Models\RescheduleRequest;
use Illuminate\Support\Facades\Auth;

class EmailLinkHandlerController extends Controller {

    public function acceptReschedule(AcceptRescheduleRequestEmailRequest $request, RescheduleRequest $rescheduleRequest) {
        run_action(RescheduleRequestAccept::class, $rescheduleRequest);
        Auth::user()->currentAccessToken()->delete();
        return response('Accept', 204);
    }


    public function declineReschedule(DeclineRescheduleRequestEmailRequest $request, RescheduleRequest $rescheduleRequest) {
        run_action(RescheduleRequestDecline::class, $rescheduleRequest);
        Auth::user()->currentAccessToken()->delete();
        return response('', 204);
    }

}
